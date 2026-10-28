<?php

declare(strict_types=1);

namespace App\Services\Api\Notification\Notification;

use App\Services\BaseService;

class CountUnreadNotificationService extends BaseService
{
    public function run(): int
    {
        return $this->getUser()->unreadNotifications()->count();
    }
}
