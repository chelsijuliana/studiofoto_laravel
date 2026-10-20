<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingFileController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    // GET /api/bookings/{booking}/files
    public function index(Request $request, Booking $booking)
    {
        $this->pastikanBerhakAkses($request, $booking);

        return response()->json($booking->files);
    }

    // POST /api/bookings/{booking}/files  — khusus admin
    public function store(Request $request, Booking $booking)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang bisa upload hasil foto.');
        }

        $request->validate([
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:10240', // maks 10MB per foto
            'video_link' => 'nullable|url',
            'update_status' => 'nullable|in:true,false,1,0',
        ]);

        if (! $request->hasFile('photos') && ! $request->filled('video_link')) {
            return response()->json(['message' => 'Kirim minimal 1 foto atau link video.'], 422);
        }

        // Simpan tiap foto ke storage, catat record-nya
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $foto) {
                $path = $foto->store("booking_files/{$booking->id}", 'public');

                $booking->files()->create([
                    'file_url' => Storage::disk('public')->url($path),
                    'file_type' => 'foto',
                ]);
            }
        }

        // Link video (Google Drive/YouTube) disimpan sebagai teks biasa, bukan file upload
        if ($request->filled('video_link')) {
            $booking->files()->create([
                'file_url' => $request->video_link,
                'file_type' => 'video_link',
            ]);
        }

        // Opsional: langsung ubah status booking jadi "hasil_siap"
        if ($request->boolean('update_status')) {
            $booking->update(['status' => 'hasil_siap']);

            $this->notificationService->kirim(
                $booking->user,
                'hasil_siap',
                'Hasil foto sudah siap',
                "Hasil foto untuk booking {$booking->package->nama_paket} sudah siap, cek di aplikasi.",
                $booking->id
            );
        }

        return response()->json([
            'message' => 'File berhasil diupload',
            'files' => $booking->files()->get(),
            'booking_status' => $booking->status,
        ], 201);
    }

    private function pastikanBerhakAkses(Request $request, Booking $booking): void
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $booking->user_id !== $user->id) {
            abort(403, 'Kamu tidak punya akses ke file booking ini.');
        }
    }
}