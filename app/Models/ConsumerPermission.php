<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerPermission extends Model
{
    use HasFactory;
    protected $table = 'consumers_permissions';
    protected $fillable = ['consumer_id', 'permission_id'];

}
