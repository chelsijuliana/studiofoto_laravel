<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public string $error = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $this->email)->first();

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            $this->error = 'Email atau password salah.';
            return;
        }

        if ($user->role !== 'admin') {
            $this->error = 'Akun ini bukan akun admin.';
            return;
        }

        Auth::login($user);
        session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}