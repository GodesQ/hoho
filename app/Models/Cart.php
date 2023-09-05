<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'carts';
    protected $fillable = [
        'user_id',
        'tour_id',
        'trip_date',
        'type',
        'number_of_pass',
        'ticket_pass',
        'amount'
    ];

    protected $casts = [
        'amount' => 'double'
    ];

    public function tour() {
        return $this->hasOne(Tour::class, 'id', 'tour_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
