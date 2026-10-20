<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    // Konfirmasi hapus
    public ?int    $konfirmasiHapusId   = null;
    public string  $konfirmasiHapusNama = '';

    public function bukaKonfirmasiHapus(int $userId): void
    {
        $user = User::find($userId);
        if (! $user) return;

        // Tidak boleh hapus diri sendiri
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Tidak bisa menghapus akun yang sedang digunakan.');
            return;
        }

        $this->konfirmasiHapusId   = $userId;
        $this->konfirmasiHapusNama = $user->nama;
    }

    public function batalHapus(): void
    {
        $this->konfirmasiHapusId   = null;
        $this->konfirmasiHapusNama = '';
    }

    public function hapusUser(): void
    {
        if (! $this->konfirmasiHapusId) return;

        $user = User::find($this->konfirmasiHapusId);

        if (! $user) {
            session()->flash('error', 'User tidak ditemukan.');
            $this->batalHapus();
            return;
        }

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Tidak diizinkan menghapus akun ini.');
            $this->batalHapus();
            return;
        }

        $nama = $user->nama;
        $user->delete();

        $this->batalHapus();
        $this->resetPage();
        session()->flash('message', "Akun \"{$nama}\" berhasil dihapus.");
    }

    public function render()
    {
        // Halaman ini murni untuk kelola PELANGGAN (akun yang lahir dari
        // registrasi lewat aplikasi), akun admin (dibuat manual/di-seed)
        // sengaja dikecualikan total di sini.
        $query = User::withCount('bookings')->where('role', 'pelanggan')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->paginate(10);

        return view('livewire.admin.user-index', ['users' => $users]);
    }
}