<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetSwipe;

use App\Models\Pet;
use App\Models\PetMatch;
use App\Models\Swipe;
use App\Models\User;
use App\Notifications\Pet\NewMatchNotification;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePetSwipeService extends BaseService
{
    private const POSITIVE_ACTIONS = ['like', 'superlike'];

    public function run(Pet $pet, array $data): array
    {
        $targetUuid = $data['target_pet_uuid'];
        $action = $data['action'];

        $target = Pet::where('uuid', $targetUuid)->with('goal')->firstOrFail();

        $this->ensureCanSwipe($pet->loadMissing('goal'), $target);

        $result = DB::transaction(function () use ($pet, $target, $action): array {
            $swipe = Swipe::create([
                'action' => $action,
                'swiper_pet_id' => $pet->id,
                'target_pet_id' => $target->id,
            ]);

            $match = null;

            if (in_array($action, self::POSITIVE_ACTIONS, true) && $this->targetAlreadyLiked($pet, $target)) {
                [$one, $two] = $pet->id < $target->id ? [$pet->id, $target->id] : [$target->id, $pet->id];

                $match = PetMatch::firstOrCreate(
                    ['pet_one_id' => $one, 'pet_two_id' => $two],
                    ['is_active' => true, 'matched_at' => now()],
                )->load(['petOne.species', 'petOne.photos', 'petTwo.species', 'petTwo.photos']);
            }

            return ['swipe' => $swipe, 'target' => $target, 'match' => $match];
        });

        $match = $result['match'];

        if ($match?->wasRecentlyCreated) {
            User::find($target->user_id)?->notify(new NewMatchNotification($match->uuid, $target->name, $pet->name));
        }

        return $result;
    }

    private function ensureCanSwipe(Pet $pet, Pet $target): void
    {
        $error = match (true) {
            ! $pet->is_active => 'Reative o seu pet para curtir outros pets.',
            $pet->goal->slug === 'adoption' || $target->goal->slug === 'adoption' => 'Pets para adoção não participam do match entre pets.',
            $target->user_id === $pet->user_id => 'Você não pode curtir um pet seu.',
            Swipe::withTrashed()->where('swiper_pet_id', $pet->id)->where('target_pet_id', $target->id)->exists() => 'Você já curtiu ou passou neste pet.',
            default => null,
        };

        if ($error !== null) {
            throw ValidationException::withMessages(['target_pet_uuid' => [$error]]);
        }
    }

    private function targetAlreadyLiked(Pet $pet, Pet $target): bool
    {
        return Swipe::where('swiper_pet_id', $target->id)
            ->where('target_pet_id', $pet->id)
            ->whereIn('action', self::POSITIVE_ACTIONS)
            ->exists();
    }
}
