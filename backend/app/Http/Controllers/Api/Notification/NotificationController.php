<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Notification\Notification\IndexNotificationRequest;
use App\Http\Resources\Notification\NotificationResource;
use App\Services\Api\Notification\Notification\CountUnreadNotificationService;
use App\Services\Api\Notification\Notification\IndexNotificationService;
use App\Services\Api\Notification\Notification\ReadAllNotificationService;
use App\Services\Api\Notification\Notification\ReadNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends BaseController
{
    public function index(
        IndexNotificationRequest $indexNotificationRequest,
        IndexNotificationService $indexNotificationService
    ): AnonymousResourceCollection {
        $data = $indexNotificationRequest->validated();
        $notifications = $indexNotificationService->run($data);

        return NotificationResource::collection($notifications);
    }

    public function unreadCount(CountUnreadNotificationService $countUnreadNotificationService): JsonResponse
    {
        $count = $countUnreadNotificationService->run();

        return $this->successResponse(['unread' => $count], 'Contagem carregada com sucesso.');
    }

    public function read(
        DatabaseNotification $notification,
        ReadNotificationService $readNotificationService
    ): JsonResponse {
        $notification = $readNotificationService->run($notification);

        return $this->successResponse(new NotificationResource($notification), 'Notificação marcada como lida.');
    }

    public function readAll(ReadAllNotificationService $readAllNotificationService): JsonResponse
    {
        $count = $readAllNotificationService->run();

        return $this->successResponse(['read' => $count], 'Notificações marcadas como lidas.');
    }
}
