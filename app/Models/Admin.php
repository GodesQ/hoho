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
    protected $fillable = ['username', 'email', 'admin_profile', 'password', 'firstname', 'lastname', 'middlename', 'age', 'birthdate', 'contact_no', 'address', 'role', 'is_active'];
    protected $hidden = ['password'];

    protected $casts = [
        'age' => 'integer',
        'is_active' => 'integer'
    ];

    public function transport()
    {
        // Check if the role is 'bus_operator'
        return $this->belongsTo(Transport::class, 'id', 'operator_id')->when($this->role === 'bus_operator', function ($query) {
            $query->where('role', 'bus_operator');
        })->select('id', 'capacity', 'operator_id', 'tour_assigned_id', 'tour_assignment_ids', 'latitude', 'longitude', 'name', 'current_location', 'next_location', 'previous_location')->with('assigned_tour');
    }
}
