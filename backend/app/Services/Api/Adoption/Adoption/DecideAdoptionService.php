<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\Adoption;

use App\Models\AdoptionMatch;
use App\Models\Pet;
use App\Models\User;
use App\Notifications\Adoption\AdoptionDecisionNotification;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DecideAdoptionService extends BaseService
{
    public function run(AdoptionMatch $adoptionMatch, array $data): AdoptionMatch
    {
        $status = $data['status'];
        $userId = $this->getUserId();
        $adoptionMatch->load('pet');

        if (! Pet::managedBy($userId)->whereKey($adoptionMatch->pet_id)->exists()) {
            abort(403, 'Apenas o dono do pet ou os membros da ONG podem decidir o interesse.');
        }

        if (! $adoptionMatch->canTransitionTo($status)) {
            throw ValidationException::withMessages(['status' => [
                'Não é possível alterar o interesse de "'.AdoptionMatch::STATUS_LABELS[$adoptionMatch->status].'" para "'.AdoptionMatch::STATUS_LABELS[$status].'".',
            ]]);
        }

        $adoption = DB::transaction(function () use ($adoptionMatch, $status, $userId): AdoptionMatch {
            $adoptionMatch->update(['status' => $status, 'decided_at' => now(), 'decided_by_user_id' => $userId]);

            if ($status === 'adopted') {
                $adoptionMatch->pet->update(['is_active' => false]);

                AdoptionMatch::where('pet_id', $adoptionMatch->pet_id)
                    ->where('id', '!=', $adoptionMatch->id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->update(['status' => 'declined', 'decided_at' => now(), 'decided_by_user_id' => $userId]);
            }

            return $adoptionMatch->refresh()->load(['pet.species', 'pet.breed', 'pet.size', 'pet.goal', 'pet.photos', 'pet.ong', 'user']);
        });

        User::find($adoption->user_id)?->notify(new AdoptionDecisionNotification($adoption->uuid, $adoption->pet->name, $status));

        return $adoption;
    }
}
