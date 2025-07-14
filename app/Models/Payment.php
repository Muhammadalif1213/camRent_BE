<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     */
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'proof_path',
        'confirmed_by_admin_id',
    ];

    /**
     * Mendefinisikan relasi bahwa satu pembayaran dimiliki oleh satu booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Mendefinisikan relasi bahwa satu pembayaran dikonfirmasi oleh satu admin (User).
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_admin_id');
    }
}