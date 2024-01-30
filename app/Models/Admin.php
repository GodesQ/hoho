<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $table = 'admins';
    protected $fillable = [
        'username',
        'email',
        'admin_profile',
        'password',
        'firstname',
        'lastname',
        'middlename',
        'age',
        'birthdate',
        'contact_no',
        'address',
        'role',
        'is_active',
        'is_merchant',
        'is_approved',
        'merchant_data_id',
        'merchant_id',
        'transport_id',
        'merchant_email_approved_at'
    ];
    protected $hidden = ['password'];

    protected $casts = [
        'age' => 'integer',
        'is_active' => 'integer'
    ];

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id')->select('id', 'capacity', 'operator_id', 'tour_assigned_id', 'tour_assignment_ids', 'latitude', 'longitude', 'name', 'current_location', 'next_location', 'previous_location')->with('assigned_tour');
    }

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}