<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Profile\Profile\UpdateProfileAvatarRequest;
use App\Http\Requests\Api\Profile\Profile\UpdateProfilePasswordRequest;
use App\Http\Requests\Api\Profile\Profile\UpdateProfileRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Api\Profile\Profile\DeleteProfileAvatarService;
use App\Services\Api\Profile\Profile\UpdateProfileAvatarService;
use App\Services\Api\Profile\Profile\UpdateProfilePasswordService;
use App\Services\Api\Profile\Profile\UpdateProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController extends BaseController
{
    public function update(
        UpdateProfileRequest $updateProfileRequest,
        UpdateProfileService $updateProfileService
    ): JsonResponse {
        $data = $updateProfileRequest->validated();
        $user = $updateProfileService->run($data);

        return $this->successResponse(new AuthResource($user), 'Perfil atualizado com sucesso.');
    }

    public function avatar(
        UpdateProfileAvatarRequest $updateProfileAvatarRequest,
        UpdateProfileAvatarService $updateProfileAvatarService
    ): JsonResponse {
        $data = $updateProfileAvatarRequest->validated();
        $user = $updateProfileAvatarService->run($data);

        return $this->successResponse(new AuthResource($user), 'Foto de perfil atualizada com sucesso.');
    }

    public function removeAvatar(DeleteProfileAvatarService $deleteProfileAvatarService): JsonResponse
    {
        $user = $deleteProfileAvatarService->run();

        return $this->successResponse(new AuthResource($user), 'Foto de perfil removida com sucesso.');
    }

    public function password(
        UpdateProfilePasswordRequest $updateProfilePasswordRequest,
        UpdateProfilePasswordService $updateProfilePasswordService
    ): JsonResponse {
        $data = $updateProfilePasswordRequest->validated();
        $updateProfilePasswordService->run($data);

        return $this->successResponse(null, 'Senha alterada com sucesso. Os outros dispositivos foram desconectados.');
    }
}
