<?php

declare(strict_types=1);

namespace App\Services\Api\Notification\Notification;

use App\Services\BaseService;

class ReadAllNotificationService extends BaseService
{
    public function run(): int
    {
        return $this->getUser()->unreadNotifications()->update(['read_at' => now()]);
    }
}
