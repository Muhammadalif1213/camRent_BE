<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name'];
    protected $table = 'customer';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
