<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama/model kamera
            $table->string('brand'); // Merek kamera (e.g., Canon, Sony)
            $table->text('description'); // Deskripsi dan spesifikasi
            $table->decimal('rental_price_per_day', 10, 2); // Harga sewa per hari
            $table->string('image_url'); // Path atau URL ke gambar kamera
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cameras');
    }
};
