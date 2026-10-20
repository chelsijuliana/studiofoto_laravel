<?php

namespace App\Livewire\Admin;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $showDropdown = false;

    public function toggleDropdown()
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function bacaNotifikasi(int $id)
    {
        $notif = AppNotification::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['is_read' => true]);

        if (! $notif->booking_id) {
            return;
        }

        // Notifikasi chat baru harus mengarah langsung ke room chat dengan
        // pelanggan tersebut, bukan ke detail booking.
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
        $notifications = AppNotification::where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get();

        $jumlahBelumDibaca = AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('livewire.admin.notification-bell', [
            'notifications' => $notifications,
            'jumlahBelumDibaca' => $jumlahBelumDibaca,
        ]);
    }
}