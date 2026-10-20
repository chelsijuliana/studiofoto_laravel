<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatShow extends Component
{
    public Booking $booking;
    public string $pesanBaru = '';

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(
            'user', 'package.category', 'sessions', 'chatMessages', 'payments'
        );

        $this->booking->chatMessages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function sendMessage(NotificationService $notificationService)
    {
        $this->validate(['pesanBaru' => 'required|string|max:1000']);

        $pesan = trim($this->pesanBaru);

        $this->booking->chatMessages()->create([
            'sender_id'   => Auth::id(),
            'sender_type' => 'admin',
            'pesan'       => $pesan,
            'is_read'     => false,
        ]);

        // Kirim notifikasi ke pelanggan — pakai User object sesuai signature
        $notificationService->kirim(
            $this->booking->user,
            'chat_baru',
            'Pesan baru dari Admin',
            $pesan,
            $this->booking->id,
        );

        $this->pesanBaru = '';
        $this->booking = $this->booking->fresh(
            'user', 'package.category', 'sessions', 'chatMessages', 'payments'
        );
    }

    public function render()
    {
        return view('livewire.admin.chat-show');
    }
}