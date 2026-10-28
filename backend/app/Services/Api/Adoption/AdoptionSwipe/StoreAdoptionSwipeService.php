<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptionSwipe;

use App\Models\AdoptionMatch;
use App\Models\AdoptionSwipe;
use App\Models\Pet;
use App\Models\User;
use App\Notifications\Adoption\AdoptionInterestNotification;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreAdoptionSwipeService extends BaseService
{
    public function run(array $data): array
    {
        $petUuid = $data['pet_uuid'];
        $direct = (bool) ($data['direct'] ?? false);
        $action = $direct ? 'like' : $data['action'];
        $userId = $this->getUserId();
        $pet = Pet::where('uuid', $petUuid)->with('goal')->firstOrFail();
        $existing = AdoptionMatch::where('user_id', $userId)->where('pet_id', $pet->id)->first();

        $error = match (true) {
            $pet->goal->slug !== 'adoption' => 'Este pet não está disponível para adoção.',
            Pet::managedBy($userId)->whereKey($pet->id)->exists() => 'Você não pode adotar um pet seu.',
            ! $direct && AdoptionSwipe::withTrashed()->where('user_id', $userId)->where('pet_id', $pet->id)->exists() => 'Você já curtiu ou passou neste pet.',
            $existing?->status === 'declined' => 'O responsável recusou o seu interesse neste pet.',
            $existing !== null && $existing->status !== 'cancelled' => 'Você já entrou em contato para adotar este pet. Acompanhe em Adoções.',
            default => null,
        };

        if ($error !== null) {
            throw ValidationException::withMessages(['pet_uuid' => [$error]]);
        }

        $result = DB::transaction(function () use ($userId, $pet, $action, $direct, $existing): array {
            $swipe = $direct
                ? AdoptionSwipe::withTrashed()->firstOrNew(['user_id' => $userId, 'pet_id' => $pet->id])
                : new AdoptionSwipe(['user_id' => $userId, 'pet_id' => $pet->id]);
            $swipe->action = $action;
            $swipe->deleted_at = null;
            $swipe->save();

            if ($action === 'dislike') {
                $adoption = null;
            } elseif ($existing) {
                $existing->update(['status' => 'pending', 'decided_at' => null, 'decided_by_user_id' => null]);
                $adoption = $existing;
            } else {
                $adoption = AdoptionMatch::create(['user_id' => $userId, 'pet_id' => $pet->id, 'status' => 'pending']);
            }

            $adoption?->load(['pet.species', 'pet.breed', 'pet.size', 'pet.goal', 'pet.photos', 'pet.ong', 'user']);

            return ['swipe' => $swipe, 'pet' => $pet, 'adoption' => $adoption];
        });

        $adoption = $result['adoption'];

        if ($adoption) {
            $adopterName = $adoption->user->name;

            $pet->managers()->get()->each(fn (User $manager) => $manager->notify(new AdoptionInterestNotification($adoption->uuid, $pet->name, $adopterName)));
        }

        return $result;
    }
}
