<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand',
        'description',
        'rental_price_per_day',
        'foto_camera',
        'status',
    ];
    // Relasi: Satu kamera bisa dimiliki oleh banyak rental
    public function rentals()
    {
        return $this->belongsToMany(Rental::class, 'camera_rental');
    }
}
