<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteAccountToken extends Model
{
    use HasFactory;
    
    protected $table = "delete_account_tokens";
    protected $fillable = ["email", "code", "token", "expired_at"];
}
