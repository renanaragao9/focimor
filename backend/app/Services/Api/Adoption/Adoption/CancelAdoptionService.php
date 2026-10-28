<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\Adoption;

use App\Models\AdoptionMatch;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelAdoptionService extends BaseService
{
    public function run(AdoptionMatch $adoptionMatch): AdoptionMatch
    {
        $userId = $this->getUserId();

        if ($adoptionMatch->user_id !== $userId) {
            abort(403, 'Apenas quem demonstrou interesse pode cancelá-lo.');
        }

        if (! $adoptionMatch->canTransitionTo('cancelled')) {
            throw ValidationException::withMessages(['status' => [
                'Não é possível cancelar um interesse "'.AdoptionMatch::STATUS_LABELS[$adoptionMatch->status].'".',
            ]]);
        }

        return DB::transaction(function () use ($adoptionMatch, $userId): AdoptionMatch {
            $adoptionMatch->update(['status' => 'cancelled', 'decided_at' => now(), 'decided_by_user_id' => $userId]);

            return $adoptionMatch->refresh()->load(['pet.species', 'pet.breed', 'pet.size', 'pet.goal', 'pet.photos', 'pet.ong']);
        });
    }
}
