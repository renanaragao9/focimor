<?php

declare(strict_types=1);

namespace App\Services\Api\Notification\Notification;

use App\Services\BaseService;
use Illuminate\Notifications\DatabaseNotification;

class ReadNotificationService extends BaseService
{
    public function run(DatabaseNotification $notification): DatabaseNotification
    {
        $notification->markAsRead();

        return $notification->refresh();
    }
}
