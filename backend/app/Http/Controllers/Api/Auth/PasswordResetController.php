<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Services\Api\Auth\ForgotPasswordService;
use App\Services\Api\Auth\ResetPasswordService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends BaseController
{
    public function forgotPassword(
        ForgotPasswordRequest $forgotPasswordRequest,
        ForgotPasswordService $forgotPasswordService
    ): JsonResponse {
        $data = $forgotPasswordRequest->validated();
        $forgotPasswordService->run($data);

        return $this->successResponse(null, 'Se o e-mail estiver cadastrado, enviaremos o link de redefinição.');
    }

    public function resetPassword(
        ResetPasswordRequest $resetPasswordRequest,
        ResetPasswordService $resetPasswordService
    ): JsonResponse {
        $data = $resetPasswordRequest->validated();
        $reset = $resetPasswordService->run($data);

        if (! $reset) {
            return $this->errorResponse([], 'Token inválido ou expirado.', 422);
        }

        return $this->successResponse(null, 'Senha redefinida com sucesso.');
    }
}
