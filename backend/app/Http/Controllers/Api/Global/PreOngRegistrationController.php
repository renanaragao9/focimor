<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Global;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Global\ShowPreOngRegistrationStatusRequest;
use App\Http\Requests\Api\Global\StorePreOngRegistrationRequest;
use App\Http\Resources\Global\PreOngRegistrationStatusResource;
use App\Services\Api\Global\PreOngRegistration\ShowPreOngRegistrationStatusService;
use App\Services\Api\Global\PreOngRegistration\StorePreOngRegistrationService;
use Illuminate\Http\JsonResponse;

class PreOngRegistrationController extends BaseController
{
    public function store(
        StorePreOngRegistrationRequest $storePreOngRegistrationRequest,
        StorePreOngRegistrationService $storePreOngRegistrationService
    ): JsonResponse {
        $data = $storePreOngRegistrationRequest->validated();
        $registration = $storePreOngRegistrationService->run($data);

        return $this->successResponse(
            new PreOngRegistrationStatusResource($registration),
            'Pré-cadastro enviado com sucesso! Entraremos em contato em breve.',
            201
        );
    }

    public function status(
        ShowPreOngRegistrationStatusRequest $showPreOngRegistrationStatusRequest,
        ShowPreOngRegistrationStatusService $showPreOngRegistrationStatusService
    ): JsonResponse {
        $data = $showPreOngRegistrationStatusRequest->validated();
        $contact = $data['contact'];
        $registration = $showPreOngRegistrationStatusService->run($contact);

        return $this->successResponse(new PreOngRegistrationStatusResource($registration), 'Pré-cadastro encontrado com sucesso.');
    }
}
