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
        Schema::table('cameras', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn('image_url');

            // Tambahkan kolom baru bertipe longblob (binary)
            $table->binary('foto_camera')->nullable(); // Untuk menyimpan gambar sebagai binary
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cameras', function (Blueprint $table) {
            $table->dropColumn('foto_camera');

            // Kembalikan kolom image_url jika dibutuhkan
            $table->string('image_url')->nullable();
        });
    }
};
