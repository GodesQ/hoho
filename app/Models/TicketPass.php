<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPass extends Model
{
    use HasFactory;
    protected $table = 'ticket_passes';
    protected $fillable = ['name', 'ticket_image', 'price'];
    protected $hidden = ['created_at', 'updated_at'];

}
