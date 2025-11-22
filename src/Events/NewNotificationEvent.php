<?php

namespace Slsabil\NotificationCenter\Events;

use Illuminate\Queue\SerializesModels;
use Slsabil\NotificationCenter\Models\Notification;

class NewNotificationEvent
{
    use SerializesModels;

    public Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }
}
