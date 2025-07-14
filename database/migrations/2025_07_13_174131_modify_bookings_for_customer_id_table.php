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
        Schema::table('bookings', function (Blueprint $table) {
            // 1. Hapus kolom yang tidak diperlukan lagi
            $table->dropColumn(['payment_status', 'payment_proof_path']);

            // 2. Tambahkan kolom baru setelah kolom 'admin_notes'
            $table->text('location')->after('admin_notes')->nullable();
            $table->string('id_card_image_path')->after('location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Logika untuk mengembalikan perubahan jika diperlukan (rollback)
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_proof_path')->nullable();
            
            $table->dropColumn(['location', 'id_card_image_path']);
        });
    }
};
