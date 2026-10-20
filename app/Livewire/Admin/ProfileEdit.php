<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfileEdit extends Component
{
    public string $nama = '';
    public string $email = '';
    public string $noHp = '';

    public string $passwordLama = '';
    public string $passwordBaru = '';
    public string $passwordBaru_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->noHp = $user->no_hp ?? '';
    }

    public function simpanProfil()
    {
        $this->validate([
            'nama' => 'required|string',
            'email' => 'required|email',
            'noHp' => 'nullable|string',
        ]);

        Auth::user()->update([
            'nama' => $this->nama,
            'email' => $this->email,
            'no_hp' => $this->noHp,
        ]);

        session()->flash('message', 'Profil berhasil diperbarui.');
        $this->dispatch('profile-saved');
    }

    public function gantiPassword()
    {
        $this->validate([
            'passwordLama' => 'required',
            'passwordBaru' => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($this->passwordLama, Auth::user()->password)) {
            $this->addError('passwordLama', 'Password lama salah.');
            return;
        }

        Auth::user()->update(['password' => Hash::make($this->passwordBaru)]);

        $this->passwordLama = '';
        $this->passwordBaru = '';
        $this->passwordBaru_confirmation = '';

        session()->flash('message', 'Password berhasil diubah.');
        $this->dispatch('password-changed');
    }

    public function render()
    {
        return view('livewire.admin.profile-edit');
    }
}