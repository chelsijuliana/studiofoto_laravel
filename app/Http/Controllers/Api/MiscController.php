<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortfolioImage;
use Illuminate\Http\Request;

class MiscController extends Controller
{
    // GET /api/bookings — riwayat booking milik pelanggan yang login
    public function myBookings(Request $request)
    {
        $bookings = $request->user()->bookings()
            ->with(['package.category', 'sessions', 'items', 'payments', 'files'])
            ->withCount('chatMessages') // dipakai Flutter (ChatListScreen) buat tau chat udah pernah dimulai atau belum
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    // GET /api/portfolio — galeri portofolio, publik
    public function portfolio()
    {
        return response()->json(PortfolioImage::latest()->get());
    }
}