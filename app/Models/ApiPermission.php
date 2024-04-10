<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiPermission extends Model
{
    use HasFactory;
    protected $table = "api_permissions";
    protected $fillable = ['name', 'description'];
}
