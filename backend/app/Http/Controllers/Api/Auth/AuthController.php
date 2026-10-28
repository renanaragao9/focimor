<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\ResendVerificationCodeRequest;
use App\Http\Requests\Api\Auth\VerifyEmailRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Api\Auth\LoginService;
use App\Services\Api\Auth\LogoutService;
use App\Services\Api\Auth\RegisterService;
use App\Services\Api\Auth\ResendVerificationCodeService;
use App\Services\Api\Auth\VerifyEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function register(
        RegisterRequest $registerRequest,
        RegisterService $registerService
    ): JsonResponse {
        $data = $registerRequest->validated();
        $user = $registerService->run($data);

        return $this->successResponse([
            'me' => new AuthResource($user),
            'requires_verification' => true,
        ], 'Usuário registrado com sucesso! Verifique seu e-mail para ativar sua conta.', 201);
    }

    public function login(
        LoginRequest $loginRequest,
        LoginService $loginService
    ): JsonResponse {
        $data = $loginRequest->validated();
        $result = $loginService->run($data);
        $token = $result['token'];
        $user = $result['user'];

        return $this->successResponse([
            'token' => $token,
            'me' => new AuthResource($user),
        ], 'Login realizado com sucesso.');
    }

    public function verifyEmail(
        VerifyEmailRequest $verifyEmailRequest,
        VerifyEmailService $verifyEmailService
    ): JsonResponse {
        $data = $verifyEmailRequest->validated();
        $user = $verifyEmailService->run($data);

        if ($user === null) {
            return $this->errorResponse([], 'Código de verificação inválido ou expirado.', 422);
        }

        return $this->successResponse(new AuthResource($user), 'E-mail verificado com sucesso!');
    }

    public function resendVerificationCode(
        ResendVerificationCodeRequest $resendVerificationCodeRequest,
        ResendVerificationCodeService $resendVerificationCodeService
    ): JsonResponse {
        $data = $resendVerificationCodeRequest->validated();
        $resendVerificationCodeService->run($data);

        return $this->successResponse(null, 'Se o e-mail estiver cadastrado e pendente de verificação, enviaremos um novo código.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(new AuthResource($request->user()), 'Usuário autenticado.');
    }

    public function logout(LogoutService $logoutService): JsonResponse
    {
        $logoutService->run();

        return $this->successResponse(null, 'Logout realizado com sucesso.');
    }

    public function logoutFromAllDevices(LogoutService $logoutService): JsonResponse
    {
        $logoutService->run(allDevices: true);

        return $this->successResponse(null, 'Logout realizado em todos os dispositivos.');
    }
}
