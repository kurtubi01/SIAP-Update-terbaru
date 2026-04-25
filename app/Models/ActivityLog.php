<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'tb_activity_logs';
    protected $primaryKey = 'id_activity_log';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'activity_time',
        'activity',
        'detail',
        'ip_address',
        'device',
        'user_agent',
        'route_name',
        'http_method',
    ];

    protected function casts(): array
    {
        return [
            'activity_time' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
