<?php

namespace Slsabil\NotificationCenter\Listeners;

use Slsabil\NotificationCenter\Events\NewNotificationEvent;
use Slsabil\NotificationCenter\Jobs\SendNotification;
use Illuminate\Support\Facades\Log;

class SendNotificationListener
{
    public function handle(NewNotificationEvent $event): void
    {
        try {
            // تشغيل الـ Job مباشرة (synchronous)
            (new SendNotification($event->notification))->handle();

            // أو لو أحببت Queue:
            // dispatch(new SendNotification($event->notification));
        } catch (\Throwable $e) {
            Log::error('notification-center: error sending notification', [
                'id'      => $event->notification->id ?? null,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
