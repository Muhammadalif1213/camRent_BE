<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'admin_notes',
        'payment_status',
        //baru
        'location',
        'id_card_image_path',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Mendefinisikan relasi bahwa satu booking dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi bahwa satu booking bisa memiliki banyak kamera.
     * Ini menggunakan tabel pivot 'camera_booking' yang telah kita buat.
     */
    public function cameras(): BelongsToMany
    {
        // Nama tabel pivot bisa disesuaikan jika berbeda
        return $this->belongsToMany(Camera::class, 'camera_booking')
                    // Anda bisa menambahkan kolom dari pivot table jika perlu
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function rentalConditions() {
        return $this->hasMany(RentalCondition::class);
    }
}
