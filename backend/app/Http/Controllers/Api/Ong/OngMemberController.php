<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\OngMember\StoreOngMemberRequest;
use App\Http\Requests\Api\Ong\OngMember\UpdateOngMemberRequest;
use App\Http\Resources\Ong\OngMemberResource;
use App\Models\Ong;
use App\Models\User;
use App\Services\Api\Ong\OngMember\DeleteOngMemberService;
use App\Services\Api\Ong\OngMember\IndexOngMemberService;
use App\Services\Api\Ong\OngMember\StoreOngMemberService;
use App\Services\Api\Ong\OngMember\UpdateOngMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngMemberController extends BaseController
{
    public function index(
        Ong $managedOng,
        IndexOngMemberService $indexOngMemberService
    ): AnonymousResourceCollection {
        $members = $indexOngMemberService->run($managedOng);

        return OngMemberResource::collection($members);
    }

    public function store(
        Ong $managedOng,
        StoreOngMemberRequest $storeOngMemberRequest,
        StoreOngMemberService $storeOngMemberService
    ): JsonResponse {
        $data = $storeOngMemberRequest->validated();
        $member = $storeOngMemberService->run($managedOng, $data);

        return $this->successResponse(new OngMemberResource($member), 'Membro adicionado com sucesso.', 201);
    }

    public function update(
        Ong $managedOng,
        User $user,
        UpdateOngMemberRequest $updateOngMemberRequest,
        UpdateOngMemberService $updateOngMemberService
    ): JsonResponse {
        $data = $updateOngMemberRequest->validated();
        $member = $updateOngMemberService->run($managedOng, $user, $data);

        return $this->successResponse(new OngMemberResource($member), 'Papel atualizado com sucesso.');
    }

    public function destroy(
        Ong $managedOng,
        User $user,
        DeleteOngMemberService $deleteOngMemberService
    ): JsonResponse {
        $deleteOngMemberService->run($managedOng, $user);

        return $this->successResponse(null, 'Membro removido com sucesso.');
    }
}
