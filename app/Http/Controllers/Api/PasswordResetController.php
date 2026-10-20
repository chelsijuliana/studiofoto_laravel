<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // POST /api/forgot-password — kirim link reset ke email
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email ini tidak terdaftar di Rikoza Studio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user  = User::where('email', $request->email)->first();
        $token = Str::random(64);

        // Hapus token lama jika ada, lalu simpan yang baru
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($request->email));

        Mail::send('emails.reset-password', [
            'nama'     => $user->nama,
            'resetUrl' => $resetUrl,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->nama)
                    ->subject('Reset Password - Rikoza Studio');
        });

        return response()->json([
            'message' => 'Link reset password telah dikirim ke email kamu.',
        ]);
    }

    // GET /reset-password — halaman web form reset password
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (! $token || ! $email) {
            return response('<h2 style="font-family:sans-serif;color:red">Link tidak valid.</h2>', 400)
                ->header('Content-Type', 'text/html');
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    // POST /reset-password — proses reset password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'                 => 'required',
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.min'                   => 'Password minimal 8 karakter.',
            'password_confirmation.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cari token yang valid (maksimal 60 menit)
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return back()->withErrors(['token' => 'Link reset tidak valid atau sudah kedaluwarsa.']);
        }

        if (! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Link reset tidak valid.']);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Link reset sudah kedaluwarsa. Silakan minta link baru.']);
        }

        // Update password
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return view('auth.reset-password-success');
    }
}