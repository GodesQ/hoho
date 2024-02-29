<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiConsumer extends Model
{
    use HasFactory;

    protected $table = 'api_consumers';

    protected $fillable = ['consumer_name', 'api_code', 'api_key', 'contact_email', 'contact_phone', 'platform', 'status'];
}
