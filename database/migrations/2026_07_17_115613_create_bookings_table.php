<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('service_packages');
            $table->enum('status', [
                'menunggu_konfirmasi',
                'menunggu_pembayaran_dp',
                'confirmed',
                'sedang_diproses',
                'hasil_siap',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_konfirmasi');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->boolean('status_pengambilan_fisik')->default(false);
            $table->timestamp('tanggal_diambil')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};