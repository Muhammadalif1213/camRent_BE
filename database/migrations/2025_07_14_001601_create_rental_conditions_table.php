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
        Schema::create('rental_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('camera_id')->constrained('cameras')->onDelete('cascade');
            $table->enum('type', ['pickup', 'return']); // Jenis pengecekan: pengambilan atau pengembalian
            $table->text('notes')->nullable(); // Catatan kondisi (misal: ada baret halus)
            $table->string('photo_path'); // Path ke foto kondisi kamera
            $table->foreignId('checked_by_admin_id')->constrained('users'); // Admin yg memeriksa
            $table->timestamp('checked_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_conditions');
    }
};
