<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->integer('kuota_harian')->nullable();
            $table->boolean('butuh_ruangan')->default(true);
            $table->enum('dp_type', ['percentage', 'flat', 'none'])->default('none');
            $table->decimal('dp_value', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};