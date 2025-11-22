<?php

namespace Slsabil\NotificationCenter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Slsabil\NotificationCenter\Models\Notification;
use Slsabil\NotificationCenter\Models\NotificationRecipient;

class SendNotification
{
    use Queueable, SerializesModels;

    public Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function handle(): void
    {
        $userModel    = config('notification_center.user_model', \App\Models\User::class);
        $adminScope   = config('notification_center.admin_scope');
        $column       = $adminScope['column'] ?? 'is_superadmin';
        $value        = $adminScope['value'] ?? 1;

        $users = $userModel::query()->where($column, $value)->get();

        foreach ($users as $user) {
            NotificationRecipient::create([
                'notification_id' => $this->notification->id,
                'user_id'         => $user->id,
                'delivered_at'    => now(),
                'delivery_error'  => false,
            ]);

            // إشعار Laravel العادي (database + mail لو حابب تستغله)
            if (method_exists($user, 'notify')) {
                $user->notify(new \Slsabil\NotificationCenter\Notifications\NewNotification($this->notification));
            }
        }
    }
}
