<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = [
        'room_number',
        'type',
        'description',
        'price',
        'image',
    ];

    public function reservations()
{
    return $this->hasMany(Reservation::class);
}

}
