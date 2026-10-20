<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'                  => 'required|string|max:255',
            'email'                 => [
                'required',
                'string',
                // Validasi format email ketat (RFC5322)
                'email:rfc,dns',
                'max:255',
                'unique:users',
            ],
            'no_hp'                 => 'required|string|max:20',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ], [
            'email.email'  => 'Format email tidak valid atau domain email tidak ditemukan.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password_confirmation.same' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            $pesan = collect($validator->errors()->all())->first();
            return response()->json([
                'message' => $pesan,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $token = Str::random(64);

        $user = User::create([
            'nama'                       => $request->nama,
            'email'                      => $request->email,
            'no_hp'                      => $request->no_hp,
            'password'                   => Hash::make($request->password),
            'role'                       => 'pelanggan',
            'email_verification_token'   => $token,
            'email_verified_at'          => null,
        ]);

        // Kirim email verifikasi
        $verifyUrl = url('/api/verify-email/' . $token);
        try {
            Mail::send('emails.verify', [
                'nama'      => $user->nama,
                'verifyUrl' => $verifyUrl,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->nama)
                        ->subject('Verifikasi Email - Rikoza Studio');
            });
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email verifikasi: ' . $e->getMessage());
        }

        return response()->json([
            'message'         => 'Registrasi berhasil! Silakan cek email kamu untuk verifikasi.',
            'email_sent_to'   => $user->email,
            'needs_verify'    => true,
        ], 201);
    }

    public function verifyEmail(string $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (! $user) {
            return response('<h2 style="font-family:sans-serif;color:red">Link verifikasi tidak valid atau sudah digunakan.</h2>', 400)
                ->header('Content-Type', 'text/html');
        }

        $user->update([
            'email_verified_at'        => now(),
            'email_verification_token' => null,
        ]);

        return response('
            <html>
            <head><meta charset="UTF-8"></head>
            <body style="font-family:sans-serif;text-align:center;padding:60px;background:#F7F5F0">
                <div style="background:white;border-radius:16px;padding:40px;max-width:400px;margin:auto;box-shadow:0 4px 20px rgba(0,0,0,.08)">
                    <div style="font-size:48px">✅</div>
                    <h2 style="color:#0F0F0F">Email Terverifikasi!</h2>
                    <p style="color:#6B6B6B">Akun Rikoza Studio kamu sudah aktif.<br>Silakan kembali ke aplikasi dan login.</p>
                    <div style="margin-top:24px;padding:12px 24px;background:#C9A84C;color:white;border-radius:8px;font-weight:bold;display:inline-block">
                        Rikoza Studio
                    </div>
                </div>
            </body>
            </html>
        ')->header('Content-Type', 'text/html');
    }

    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        $token = Str::random(64);
        $user->update(['email_verification_token' => $token]);

        $verifyUrl = url('/api/verify-email/' . $token);
        try {
            Mail::send('emails.verify', [
                'nama'      => $user->nama,
                'verifyUrl' => $verifyUrl,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->nama)
                        ->subject('Verifikasi Email - Rikoza Studio');
            });
        } catch (\Exception $e) {
            \Log::error('Gagal kirim ulang email verifikasi: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengirim email. Coba lagi nanti.'], 500);
        }

        return response()->json(['message' => 'Email verifikasi telah dikirim ulang.']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        if ($user->role !== 'pelanggan') {
            return response()->json(['message' => 'Akun ini tidak dapat login lewat aplikasi.'], 403);
        }

        // Blokir login jika belum verifikasi email
        if (! $user->email_verified_at) {
            return response()->json([
                'message'      => 'Email kamu belum diverifikasi. Silakan cek inbox atau folder spam kamu.',
                'needs_verify' => true,
                'email'        => $user->email,
            ], 403);
        }

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nama'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user->update([
            'nama'  => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ]);

        return response()->json(['message' => 'Profil berhasil diperbarui', 'user' => $user]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        if (! Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'message' => 'Password lama salah',
                'errors'  => ['password_lama' => ['Password lama tidak sesuai.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);
        return response()->json(['message' => 'Password berhasil diubah']);
    }
}