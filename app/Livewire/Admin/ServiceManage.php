<?php

namespace App\Livewire\Admin;

use App\Models\PackageAddon;
use App\Models\PackageFacility;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use Livewire\Component;

class ServiceManage extends Component
{
    public $categories;

    public bool $showPackageForm = false;
    public ?int $editingPackageId = null;
    public int $formCategoryId = 0;
    public string $namaPaket = '';
    public string $harga = '';
    public string $durasiMenit = '';
    public string $maxOrang = '';
    public string $cakupanAcara = '';
    public string $fasilitasText = '';
    public string $jumlahSesi = '';
    public string $jenisSesiText = '';
    public string $jepretanPerSesi = '';
    public string $durasiPerSesi = '';
    public string $maksKostum = '';
    public string $pilihanLatar = '';
    public string $jumlahLatar = '';

    public bool $showCategoryForm = false;
    public ?int $editingCategoryId = null;
    public string $namaKategori = '';
    public string $kuotaHarian = '';
    public string $dpType = 'none';
    public string $dpValue = '';
    public bool $butuhRuangan = true;

    public bool $showAddonForm = false;
    public ?int $editingAddonId = null;
    public int $addonCategoryId = 0;
    public string $namaAddon = '';
    public string $hargaAddon = '';
    public string $tipeAddon = 'flat';

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = ServiceCategory::with('packages.facilities', 'addons')->get();
    }

    // ===== Kategori =====
    public function bukaFormTambahKategori()
    {
        $this->editingCategoryId = null;
        $this->namaKategori = '';
        $this->kuotaHarian = '';
        $this->dpType = 'none';
        $this->dpValue = '0';
        $this->butuhRuangan = true;
        $this->showCategoryForm = true;
    }

    public function bukaFormEditKategori(int $categoryId)
    {
        $category = ServiceCategory::findOrFail($categoryId);
        $this->editingCategoryId = $category->id;
        $this->namaKategori = $category->nama_kategori;
        $this->kuotaHarian = $category->kuota_harian ?? '';
        $this->dpType = $category->dp_type;
        $this->dpValue = $category->dp_value;
        $this->butuhRuangan = $category->butuh_ruangan;
        $this->showCategoryForm = true;
    }

    public function simpanKategori()
    {
        $this->validate([
            'namaKategori' => 'required|string',
            'dpType' => 'required|in:percentage,flat,none',
            'dpValue' => 'required|numeric|min:0',
        ]);

        ServiceCategory::updateOrCreate(
            ['id' => $this->editingCategoryId],
            [
                'nama_kategori' => $this->namaKategori,
                'kuota_harian' => $this->kuotaHarian ?: null,
                'dp_type' => $this->dpType,
                'dp_value' => $this->dpValue,
                'butuh_ruangan' => $this->butuhRuangan,
            ]
        );

        $this->showCategoryForm = false;
        $this->loadCategories();
        session()->flash('message', 'Kategori berhasil disimpan.');
    }

    public function hapusKategori(int $categoryId)
    {
        $category = ServiceCategory::withCount('packages')->findOrFail($categoryId);

        if ($category->packages_count > 0) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena masih punya paket. Hapus semua paketnya dulu.');
            return;
        }

        $category->delete();
        $this->loadCategories();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    // ===== Add-on =====
    public function bukaFormTambahAddon(int $categoryId)
    {
        $this->editingAddonId = null;
        $this->addonCategoryId = $categoryId;
        $this->namaAddon = '';
        $this->hargaAddon = '';
        $this->tipeAddon = 'flat';
        $this->showAddonForm = true;
    }

    public function bukaFormEditAddon(int $addonId)
    {
        $addon = PackageAddon::findOrFail($addonId);
        $this->editingAddonId = $addon->id;
        $this->addonCategoryId = $addon->category_id;
        $this->namaAddon = $addon->nama_addon;
        $this->hargaAddon = $addon->harga;
        $this->tipeAddon = $addon->tipe;
        $this->showAddonForm = true;
    }

    public function simpanAddon()
    {
        $this->validate([
            'namaAddon' => 'required|string',
            'hargaAddon' => 'required|numeric|min:0',
            'tipeAddon' => 'required|in:flat,per_sesi,per_orang,nego',
        ]);

        PackageAddon::updateOrCreate(
            ['id' => $this->editingAddonId],
            [
                'category_id' => $this->addonCategoryId,
                'nama_addon' => $this->namaAddon,
                'harga' => $this->hargaAddon,
                'tipe' => $this->tipeAddon,
            ]
        );

        $this->showAddonForm = false;
        $this->loadCategories();
        session()->flash('message', 'Add-on berhasil disimpan.');
    }

    public function hapusAddon(int $addonId)
    {
        PackageAddon::findOrFail($addonId)->delete();
        $this->loadCategories();
        session()->flash('message', 'Add-on berhasil dihapus.');
    }

    // ===== Paket (yang sudah ada sebelumnya) =====
    public function bukaFormTambah(int $categoryId)
    {
        $this->resetForm();
        $this->formCategoryId = $categoryId;
        $this->showPackageForm = true;
    }

    public function bukaFormEdit(int $packageId)
    {
        $package = ServicePackage::with('facilities')->findOrFail($packageId);

        $this->editingPackageId = $package->id;
        $this->formCategoryId = $package->category_id;
        $this->namaPaket = $package->nama_paket;
        $this->harga = $package->harga;
        $this->durasiMenit = $package->durasi_menit ?? '';
        $this->maxOrang = $package->max_orang ?? '';
        $this->cakupanAcara = $package->cakupan_acara ?? '';
        $this->fasilitasText = $package->facilities
            ->map(fn ($f) => "{$f->item_name}:{$f->qty}")
            ->implode("\n");

        $atribut = $package->atribut_lain ?? [];
        $this->jumlahSesi = $atribut['jumlah_sesi'] ?? '';
        $this->jenisSesiText = isset($atribut['jenis_sesi']) ? implode("\n", $atribut['jenis_sesi']) : '';
        $this->jepretanPerSesi = $atribut['jepretan_per_sesi'] ?? '';
        $this->durasiPerSesi = $atribut['durasi_per_sesi'] ?? '';
        $this->maksKostum = $atribut['maks_kostum'] ?? '';
        $this->pilihanLatar = $atribut['pilihan_latar'] ?? '';

        $this->showPackageForm = true;
    }

    // Nama kategori dari paket yang sedang dibuka form-nya, dipakai untuk
    // nentuin field "Info tambahan" mana yang relevan ditampilkan
    public function getFormKategoriNamaProperty(): string
    {
        if (! $this->formCategoryId) {
            return '';
        }
        return ServiceCategory::find($this->formCategoryId)?->nama_kategori ?? '';
    }

    public function simpanPaket()
    {
        $this->validate([
            'namaPaket' => 'required|string',
            'harga' => 'required|numeric|min:0',
        ]);

        $data = [
            'category_id' => $this->formCategoryId,
            'nama_paket' => $this->namaPaket,
            'harga' => $this->harga,
            'durasi_menit' => $this->durasiMenit ?: null,
            'max_orang' => $this->maxOrang ?: null,
            'cakupan_acara' => $this->cakupanAcara ?: null,
        ];

        // Cuma "tipe" dan "butuh_ruangan" yang dilindungi (dipakai Pre Wedding,
        // diatur dari tempat lain). Field lama lainnya yang gak ada di form ini
        // (misal sisa data lama seperti jumlah_pose) sengaja DIBUANG, bukan
        // dipertahankan terus, biar form ini jadi sumber kebenaran tunggal.
        $atributLama = [];
        if ($this->editingPackageId) {
            $atributLamaPenuh = ServicePackage::find($this->editingPackageId)->atribut_lain ?? [];
            $atributLama = array_intersect_key($atributLamaPenuh, array_flip(['tipe', 'butuh_ruangan']));
        }
        // Kategori Foto Latar Biru gak punya field "Jepretan per Sesi" di form
        // ini (dia pakai "Sesi Tambahan" / jenis_sesi aja), jadi kalau paket
        // ini sebelumnya sempat kesimpan nilai jepretan_per_sesi (misal dari
        // saat kategorinya masih beda), sengaja dibuang di sini juga —
        // bukan cuma dari $atributLama, karena nilainya kadung nyangkut di
        // properti $this->jepretanPerSesi sejak form dibuka.
        $kategoriLatarBiruSaatIni = str_contains(strtolower(trim($this->formKategoriNama)), 'latar biru');
        if ($kategoriLatarBiruSaatIni) {
            $this->jepretanPerSesi = '';
        }

        $atributBaru = array_filter([
            'jumlah_sesi' => $this->jumlahSesi ? (int) $this->jumlahSesi : null,
            'jenis_sesi' => $this->jenisSesiText
                ? array_values(array_filter(array_map('trim', explode("\n", $this->jenisSesiText))))
                : null,
            'jepretan_per_sesi' => $this->jepretanPerSesi ?: null,
            'durasi_per_sesi' => $this->durasiPerSesi ?: null,
            'maks_kostum' => $this->maksKostum ? (int) $this->maksKostum : null,
            'pilihan_latar' => $this->pilihanLatar ? (int) $this->pilihanLatar : null,
            'jumlah_latar' => $this->jumlahLatar ? (int) $this->jumlahLatar : null,
        ], fn ($v) => $v !== null && $v !== []);

        $atributGabungan = array_merge($atributLama, $atributBaru);
        $data['atribut_lain'] = empty($atributGabungan) ? null : $atributGabungan;

        if ($this->editingPackageId) {
            $package = ServicePackage::findOrFail($this->editingPackageId);
            $package->update($data);
            $package->facilities()->delete();
        } else {
            $package = ServicePackage::create($data);
        }

        foreach (explode("\n", $this->fasilitasText) as $baris) {
            $baris = trim($baris);
            if (! $baris) {
                continue;
            }
            [$nama, $qty] = array_pad(explode(':', $baris), 2, 1);
            $package->facilities()->create(['item_name' => trim($nama), 'qty' => (int) trim($qty)]);
        }

        $this->showPackageForm = false;
        $this->resetForm();
        $this->loadCategories();
        session()->flash('message', 'Paket berhasil disimpan.');
    }

    public function hapusPaket(int $packageId)
    {
        ServicePackage::findOrFail($packageId)->delete();
        $this->loadCategories();
        session()->flash('message', 'Paket berhasil dihapus.');
    }

    public function batal()
    {
        $this->showPackageForm = false;
        $this->showCategoryForm = false;
        $this->showAddonForm = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingPackageId = null;
        $this->namaPaket = '';
        $this->harga = '';
        $this->durasiMenit = '';
        $this->maxOrang = '';
        $this->cakupanAcara = '';
        $this->fasilitasText = '';
        $this->jumlahSesi = '';
        $this->jenisSesiText = '';
        $this->jepretanPerSesi = '';
        $this->durasiPerSesi = '';
        $this->maksKostum = '';
        $this->pilihanLatar = '';
        $this->jumlahLatar = '';
    }

    public function render()
    {
        return view('livewire.admin.service-manage');
    }
}