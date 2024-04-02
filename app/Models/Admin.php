<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $casts = [
        'is_active' => 'integer',
        'is_merchant' => 'integer',
        'age' => 'integer',
        'is_approved' => 'integer',
        'merchant_data_id' => 'integer',
        'merchant_id' => 'integer',
        'transport_id' => 'integer',
    ];

    protected $hidden = ['password'];

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Hash::make($value)
        );
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id')->select('id', 'capacity', 'available_seats', 'operator_id', 'tour_assigned_id', 'hub_id', 'tour_assignment_ids', 'latitude', 'longitude', 'name', 'current_location', 'next_location', 'previous_location')->with('assigned_tour');
    }

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}