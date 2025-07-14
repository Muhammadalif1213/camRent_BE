<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalCondition extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     */
    protected $fillable = [
        'booking_id',
        'camera_id',
        'type',
        'notes',
        'photo_path',
        'checked_by_admin_id',
        'checked_at',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected $casts = [
        'checked_at' => 'datetime',
    ];

    /**
     * Mendefinisikan relasi bahwa satu catatan kondisi dimiliki oleh satu booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Mendefinisikan relasi bahwa satu catatan kondisi merujuk pada satu kamera.
     */
    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }

    /**
     * Mendefinisikan relasi bahwa satu catatan kondisi diperiksa oleh satu admin (User).
     */
    public function checkedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_admin_id');
    }
}