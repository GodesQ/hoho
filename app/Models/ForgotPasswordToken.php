<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPasswordToken extends Model
{
    use HasFactory;

    protected $table = 'forgot_password_tokens';
    protected $fillable = ['email', 'token'];
}
