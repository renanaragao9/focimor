<?php

namespace App\Providers;

use App\Models\AdoptionMatch;
use App\Models\Ong;
use App\Models\OngPost;
use App\Models\Pet;
use App\Models\PetMatch;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteBindingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::bind('pet', fn (string $uuid) => Pet::where('uuid', $uuid)
            ->managedBy((int) request()->user()?->id)
            ->firstOr(fn () => abort(404, 'Pet não encontrado.')));

        Route::bind('petMatch', fn (string $uuid) => PetMatch::where('uuid', $uuid)
            ->where('is_active', true)
            ->where(fn ($q) => $q
                ->whereHas('petOne', fn ($p) => $p->where('user_id', request()->user()?->id))
                ->orWhereHas('petTwo', fn ($p) => $p->where('user_id', request()->user()?->id)))
            ->firstOr(fn () => abort(404, 'Match não encontrado.')));

        Route::bind('adoptionMatch', fn (string $uuid) => AdoptionMatch::where('uuid', $uuid)
            ->where(fn ($q) => $q
                ->where('user_id', request()->user()?->id)
                ->orWhereHas('pet', fn ($p) => $p->managedBy((int) request()->user()?->id)))
            ->firstOr(fn () => abort(404, 'Adoção não encontrada.')));

        Route::bind('notification', fn (string $id) => DatabaseNotification::where('id', $id)
            ->where('notifiable_type', (new User)->getMorphClass())
            ->where('notifiable_id', request()->user()?->id)
            ->firstOr(fn () => abort(404, 'Notificação não encontrada.')));

        Route::bind('ong', fn (string $uuid) => Ong::where('uuid', $uuid)
            ->where('status', 'verified')
            ->firstOr(fn () => abort(404, 'ONG não encontrada.')));

        Route::bind('adoptablePet', fn (string $uuid) => Pet::where('uuid', $uuid)
            ->adoptable()
            ->firstOr(fn () => abort(404, 'Pet não encontrado.')));

        Route::bind('managedOng', fn (string $uuid) => Ong::where('uuid', $uuid)
            ->whereIn('id', DB::table('ong_user')->select('ong_id')->where('user_id', request()->user()?->id))
            ->firstOr(fn () => abort(404, 'ONG não encontrada.')));

        Route::bind('publicPost', fn (string $uuid) => OngPost::where('uuid', $uuid)
            ->published()
            ->firstOr(fn () => abort(404, 'Post não encontrado.')));
    }
}
