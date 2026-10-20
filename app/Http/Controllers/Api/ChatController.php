<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChatMessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    // GET /api/bookings/{booking}/messages
    public function index(Request $request, Booking $booking)
    {
        $this->pastikanBerhakAkses($request, $booking);

        // Tandai pesan dari lawan bicara sebagai sudah dibaca
        ChatMessage::where('booking_id', $booking->id)
            ->where('sender_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $booking->chatMessages()
            ->with('sender:id,nama,role')
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    // POST /api/bookings/{booking}/messages
    public function store(Request $request, Booking $booking)
    {
        $this->pastikanBerhakAkses($request, $booking);

        $request->validate([
            'pesan' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        $message = $booking->chatMessages()->create([
            'sender_id' => $user->id,
            'sender_type' => $user->role === 'admin' ? 'admin' : 'customer',
            'pesan' => $request->pesan,
            'is_read' => false,
        ]);

        if ($user->role === 'admin') {
            $this->notificationService->kirim(
                $booking->user,
                'chat_baru',
                'Admin membalas chat',
                "Admin membalas chat booking {$booking->package->nama_paket}.",
                $booking->id
            );
        } else {
            $this->notificationService->kirimKeAdmin(
                'chat_baru',
                'Chat baru dari pelanggan',
                "{$user->nama} mengirim pesan baru di booking {$booking->package->nama_paket}.",
                $booking->id
            );
        }

        return response()->json($message->load('sender:id,nama,role'), 201);
    }

    private function pastikanBerhakAkses(Request $request, Booking $booking): void
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $booking->user_id !== $user->id) {
            abort(403, 'Kamu tidak punya akses ke chat booking ini.');
        }
    }
}