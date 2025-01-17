<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_uid',
        'username',
        'email',
        'password',
        'firstname',
        'middlename',
        'lastname',
        'user_profile',
        'age',
        'birthdate',
        'gender',
        'contact_no',
        'interest_ids',
        'status',
        'is_old_user',
        'is_verify',
        'countryCode',
        'isoCode',
        'country_of_residence',
        'is_first_time_philippines',
        'is_international_tourist',
        'role',
        'login_with'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_old_user' => 'integer',
        'is_active' => 'integer',
        'age' => 'integer',
        'is_verify' => 'integer',
        'is_first_time_philippines' => 'integer',
        'is_international_tourist' => 'integer'
    ];

    protected function birthdate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d') : null
        );
    }


    // protected function password(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn($value) => Hash::make($value)
    //     );
    // }

    protected $appends = ['interests'];

    public function getInterestsAttribute()
    {
        $interest_ids = json_decode($this->interest_ids, true);

        if (is_array($interest_ids) && ! empty($interest_ids)) {
            $data = Interest::whereIn('id', $interest_ids)
                ->get()
                ->toArray();

            if (! empty($data)) {
                return $data;
            }
        }
    }

    public function user_badges()
    {
        return $this->hasMany(UserTourBadge::class, 'user_id');
    }
}
