<?php
namespace App\Services;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class LoggerService
{
    public static function log($action, $model, $context) {
        SystemLog::create([
            'action' => $action,
            'model' => $model,
            'context' => json_encode($context),
            'user_id' => Auth::id()
        ]);
    }
}
