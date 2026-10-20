<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->string('nama_addon');
            $table->decimal('harga', 12, 2)->default(0);
            $table->enum('tipe', ['flat', 'per_sesi', 'per_orang', 'nego']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_addons');
    }
};