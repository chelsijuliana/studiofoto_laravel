<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\BookingFile;
use App\Models\Payment;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookingShow extends Component
{
    use WithFileUploads;

    public Booking $booking;

    public string $status = '';
    public string $biayaTambahan = '';
    public string $pesanBaru = '';
    public array $photos = [];
    public string $videoLink = '';
    public string $jumlahPelunasan = '';
    public string $alasanTolakPembayaran = '';

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load('user', 'package.category', 'sessions', 'chatMessages.sender', 'files', 'payments', 'items');
        $this->status = $booking->status;

        // Tandai semua pesan dari pelanggan sebagai sudah dibaca
        $this->booking->chatMessages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function confirmPrice(NotificationService $notificationService)
    {
        $this->validate([
            'biayaTambahan' => 'required|numeric|min:0',
        ]);

        $this->booking->update([
            'total_harga' => $this->booking->total_harga + $this->biayaTambahan,
            'status' => 'menunggu_pembayaran_dp',
        ]);

        // Catat/update baris rincian harga "Biaya luar kota", biar pelanggan
        // lihat keterangan jelas kenapa harganya bertambah
        $itemNego = $this->booking->items()->where('keterangan', 'like', '%menunggu konfirmasi admin%')->first();
        if ($itemNego) {
            $itemNego->update([
                'keterangan' => str_replace(' (menunggu konfirmasi admin)', '', $itemNego->keterangan),
                'harga_satuan' => $this->biayaTambahan,
                'subtotal' => $this->biayaTambahan,
            ]);
        } else {
            $this->booking->items()->create([
                'keterangan' => 'Biaya luar kota',
                'qty' => 1,
                'harga_satuan' => $this->biayaTambahan,
                'subtotal' => $this->biayaTambahan,
            ]);
        }

        $notificationService->kirim(
            $this->booking->user,
            'harga_dikonfirmasi',
            'Harga final dikonfirmasi',
            "Admin telah mengonfirmasi biaya tambahan sebesar Rp".number_format($this->biayaTambahan, 0, ',', '.').".",
            $this->booking->id
        );

        $this->biayaTambahan = '';
        $this->booking->refresh();
        $this->status = $this->booking->status;
        session()->flash('message', 'Harga berhasil dikonfirmasi.');
    }

    public function updateStatus(NotificationService $notificationService)
    {
        // Cegah pembatalan jika sudah ada pembayaran DP yang berhasil
        if ($this->status === 'dibatalkan') {
            $sudahBayarDp = $this->booking->payments()
                ->where('jenis', 'dp')
                ->where('status', 'success')
                ->exists();

            if ($sudahBayarDp) {
                session()->flash('error', 'Booking tidak bisa dibatalkan karena pelanggan sudah membayar DP. Hubungi pelanggan terlebih dahulu untuk proses refund.');
                return;
            }
        }

        $statusLama = $this->booking->status;
        $this->booking->update(['status' => $this->status]);

        // Kabari pelanggan kalau statusnya berubah ke "sedang diproses" atau
        // "dibatalkan" (perubahan status lain seperti "hasil_siap" sudah ada
        // notifikasinya sendiri lewat BookingFileController/uploadFiles()).
        if ($this->status !== $statusLama && $this->status === 'sedang_diproses') {
            $notificationService->kirim(
                $this->booking->user,
                'sedang_diproses',
                'Hasil foto sedang diproses',
                "Sesi foto {$this->booking->package->nama_paket} kamu sudah selesai, hasilnya sedang diproses oleh tim studio.",
                $this->booking->id
            );
        }

        if ($this->status !== $statusLama && $this->status === 'dibatalkan') {
            $notificationService->kirim(
                $this->booking->user,
                'booking_dibatalkan',
                'Booking dibatalkan',
                "Booking {$this->booking->package->nama_paket} kamu telah dibatalkan oleh admin.",
                $this->booking->id
            );
        }

        $this->dispatch('status-berhasil', status: $this->status);
    }

    public function togglePickedUp()
    {
        $this->booking->update([
            'status_pengambilan_fisik' => ! $this->booking->status_pengambilan_fisik,
            'tanggal_diambil' => ! $this->booking->status_pengambilan_fisik ? now() : null,
        ]);
        $this->booking->refresh();
    }

    // Catat pembayaran cash manual (misal pelunasan di hari-H)
    public function catatPelunasanCash(NotificationService $notificationService)
    {
        $this->validate([
            'jumlahPelunasan' => 'required|numeric|min:1',
        ]);

        if ($this->jumlahPelunasan > $this->booking->sisaPelunasan()) {
            $this->addError('jumlahPelunasan', 'Jumlah melebihi sisa pelunasan.');
            return;
        }

        Payment::create([
            'booking_id' => $this->booking->id,
            'jenis' => 'pelunasan',
            'jumlah' => $this->jumlahPelunasan,
            'metode_pembayaran' => 'cash',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $notificationService->kirim(
            $this->booking->user,
            'pembayaran_berhasil',
            'Pembayaran pelunasan berhasil',
            "Pembayaran pelunasan sebesar Rp".number_format($this->jumlahPelunasan, 0, ',', '.')." untuk booking {$this->booking->package->nama_paket} tercatat.",
            $this->booking->id
        );

        $this->jumlahPelunasan = '';
        $this->booking->refresh();
        session()->flash('message', 'Pelunasan cash berhasil dicatat.');
    }

    // ── TRANSFER MANUAL: verifikasi & tolak bukti transfer ──────────────
    // (menggantikan pengecekan otomatis dari payment gateway/Midtrans yang
    // dulu dipakai — sekarang admin yang cek bukti transfernya langsung)

    public function verifikasiPembayaran(int $paymentId, PaymentService $paymentService)
    {
        $payment = $this->booking->payments()
            ->where('id', $paymentId)
            ->where('status', 'pending')
            ->where('metode_pembayaran', 'transfer_manual')
            ->first();

        if (! $payment) {
            session()->flash('error', 'Pembayaran tidak ditemukan atau sudah diproses sebelumnya.');
            return;
        }

        $paymentService->setujuiPembayaranManual($payment);

        $this->booking->refresh();
        $this->status = $this->booking->status;
        session()->flash('message', 'Bukti transfer disetujui, pembayaran tercatat berhasil.');
    }

    public function tolakPembayaran(int $paymentId, PaymentService $paymentService)
    {
        $payment = $this->booking->payments()
            ->where('id', $paymentId)
            ->where('status', 'pending')
            ->where('metode_pembayaran', 'transfer_manual')
            ->first();

        if (! $payment) {
            session()->flash('error', 'Pembayaran tidak ditemukan atau sudah diproses sebelumnya.');
            return;
        }

        $paymentService->tolakPembayaranManual($payment, $this->alasanTolakPembayaran ?: null);

        $this->alasanTolakPembayaran = '';
        $this->booking->refresh();
        session()->flash('message', 'Bukti transfer ditolak, pelanggan akan diminta upload ulang.');
    }

    public function sendMessage(NotificationService $notificationService)
    {
        $this->validate(['pesanBaru' => 'required|string|max:1000']);

        $this->booking->chatMessages()->create([
            'sender_id' => Auth::id(),
            'sender_type' => 'admin',
            'pesan' => $this->pesanBaru,
            'is_read' => false,
        ]);

        $notificationService->kirim(
            $this->booking->user,
            'chat_baru',
            'Admin membalas chat',
            "Admin membalas chat booking {$this->booking->package->nama_paket}.",
            $this->booking->id
        );

        $this->pesanBaru = '';
        $this->booking->refresh();
    }

    public function uploadFiles(NotificationService $notificationService)
    {
        $this->validate([
            'photos.*' => 'nullable|image|max:30720',
            'videoLink' => 'nullable|url',
        ]);

        foreach ($this->photos as $foto) {
            $path = $foto->store("booking_files/{$this->booking->id}", 'public');
            $this->booking->files()->create([
                'file_url' => Storage::disk('public')->url($path),
                'file_type' => 'foto',
            ]);
        }

        if ($this->videoLink) {
            $this->booking->files()->create([
                'file_url' => $this->videoLink,
                'file_type' => 'video_link',
            ]);
        }

        $this->booking->update(['status' => 'hasil_siap']);

        $notificationService->kirim(
            $this->booking->user,
            'hasil_siap',
            'Hasil foto sudah siap',
            "Hasil foto untuk booking {$this->booking->package->nama_paket} sudah siap.",
            $this->booking->id
        );

        $this->photos = [];
        $this->videoLink = '';
        $this->booking->refresh();
        $this->status = $this->booking->status;
        session()->flash('message', 'File berhasil diupload.');
    }

    // Hapus 1 file hasil (foto/video) yang salah upload
    public function hapusFile(int $fileId)
    {
        $file = BookingFile::where('booking_id', $this->booking->id)->findOrFail($fileId);
        $file->delete();
        $this->booking->refresh();
        session()->flash('message', 'File berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.booking-show');
    }
}