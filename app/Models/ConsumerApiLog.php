<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerApiLog extends Model
{
    use HasFactory;

    protected $table = 'consumers_api_logs';

    protected $fillable = ['consumer_id', 'request_timestamp', 'http_method', 'request_path', 'request_headers', 'request_body', 'ip_address', 'user_agent'];

    protected $dates = ['request_timestamp'];

    public function consumer() {
        return $this->belongsTo(ApiConsumer::class, 'consumer_id');
    }
}
