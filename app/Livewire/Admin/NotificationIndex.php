<?php

namespace App\Livewire\Admin;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationIndex extends Component
{
    use WithPagination;

    public function bacaNotifikasi(int $id)
    {
        $notif = AppNotification::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['is_read' => true]);

        if (! $notif->booking_id) {
            return;
        }

        // Notifikasi chat baru harus mengarah langsung ke room chat dengan
        // pelanggan tersebut, bukan ke detail booking. Sama seperti di
        // NotificationBell.php, biar perilakunya konsisten.
        if ($notif->type === 'chat_baru') {
            return redirect()->route('admin.chat.show', $notif->booking_id);
        }

        return redirect()->route('admin.bookings.show', $notif->booking_id);
    }

    public function tandaiSemuaDibaca()
    {
        AppNotification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
    }

    public function render()
    {
        // Beda dari NotificationBell.php yang cuma limit(10), di sini pakai
        // pagination, jadi SEMUA notifikasi (lama maupun baru) tetap bisa
        // dijangkau, gak ada yang "hilang" begitu ada notif baru numpuk.
        $notifications = AppNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        $jumlahBelumDibaca = AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('livewire.admin.notification-index', [
            'notifications' => $notifications,
            'jumlahBelumDibaca' => $jumlahBelumDibaca,
        ]);
    }
}