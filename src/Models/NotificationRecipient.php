<?php

namespace Slsabil\NotificationCenter\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRecipient extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'read_at',
        'delivered_at',
        'delivery_error',
    ];

    protected $casts = [
        'read_at'      => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        $userModel = config('notification_center.user_model', \App\Models\User::class);
        return $this->belongsTo($userModel, 'user_id');
    }
}
