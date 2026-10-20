<?php

namespace App\Livewire\Admin;

use App\Models\PortfolioImage;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PortfolioManage extends Component
{
    use WithFileUploads;

    public array $photos = [];
    public string $kategori = '';

    // Daftar kategori yang tersedia
    public array $daftarKategori = [
        'Wedding',
        'Pre Wedding',
        'Family',
        'Graduation',
        'Maternity',
        'Pass Foto',
        'Foto Latar Biru',
    ];

    public function simpanFoto()
    {
        $this->validate([
            'photos.*' => 'required|image|max:30720',
            'kategori' => 'nullable|string|max:100',
        ]);

        foreach ($this->photos as $foto) {
            $path = $foto->store('portfolio', 'public');

            PortfolioImage::create([
                'image_url'   => Storage::disk('public')->url($path),
                'kategori'    => $this->kategori ?: null,
                'uploaded_at' => now(),
            ]);
        }

        $this->photos   = [];
        $this->kategori = '';
        session()->flash('message', 'Foto berhasil diupload.');
    }

    public function hapus(int $id)
    {
        PortfolioImage::findOrFail($id)->delete();
        session()->flash('message', 'Foto berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.portfolio-manage', [
            'images' => PortfolioImage::latest()->get(),
        ]);
    }
}