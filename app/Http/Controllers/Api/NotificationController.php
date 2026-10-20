<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // POST /api/device-tokens — dipanggil Flutter setiap kali app dibuka/login
    public function storeDeviceToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'platform' => 'required|in:android,ios',
        ]);

        // Kalau token yang sama udah pernah tersimpan (misal dari device lain
        // yang sekarang login user ini), update aja, jangan bikin baris baru
        $token = DeviceToken::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            ['user_id' => $request->user()->id, 'platform' => $request->platform]
        );

        return response()->json(['message' => 'Device token tersimpan', 'data' => $token]);
    }

    // GET /api/notifications — list notifikasi milik user yang login
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    // PATCH /api/notifications/{id}/read — tandai 1 notifikasi sudah dibaca
    public function markAsRead(Request $request, int $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca']);
    }

    // PATCH /api/notifications/read-all — tandai semua sudah dibaca sekaligus
    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca']);
    }
}