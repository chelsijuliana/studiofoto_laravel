<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration ini menambahkan kolom yang dibutuhkan untuk sistem pembayaran
// TRANSFER MANUAL (upload bukti transfer), menggantikan payment gateway
// (Midtrans) yang sebelumnya dipakai. Kolom gateway lama (gateway_transaction_id)
// tetap dibiarkan ada, tidak dihapus, biar data lama & kode gateway yang
// dikomen di PaymentService/PaymentController tetap konsisten kalau suatu
// saat mau diaktifkan lagi.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Path/URL foto bukti transfer yang diupload pelanggan
            $table->string('bukti_transfer')->nullable()->after('metode_pembayaran');
            // Catatan admin saat verifikasi/menolak bukti transfer (opsional)
            $table->string('catatan_admin')->nullable()->after('bukti_transfer');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['bukti_transfer', 'catatan_admin']);
        });
    }
};