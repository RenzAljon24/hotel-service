<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use App\Models\User;
class Transaction extends Model
{
    use HasFactory;

    // Define the table name (optional if following Laravel conventions)
    protected $table = 'transactions';

    // Define the fillable attributes to protect against mass assignment
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'total_price',
        'room_image',
        'status',
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Room model
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
