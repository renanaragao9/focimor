<?php

declare(strict_types=1);

namespace App\Services\Api\Global\PreOngRegistration;

use App\Models\Ong;
use App\Models\PreOngRegistration;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovePreOngRegistrationService extends BaseService
{
    public function run(PreOngRegistration $registration): Ong
    {
        return DB::transaction(function () use ($registration): Ong {
            $ong = Ong::create([
                'name' => $registration->name,
                'slug' => $this->uniqueSlug($registration->name),
                'cnpj' => $registration->cnpj,
                'description' => $registration->description,
                'email' => $registration->email,
                'phone' => $registration->phone,
                'city' => $registration->city,
                'state' => $registration->state,
                'status' => 'verified',
                'verified_at' => now(),
            ]);

            $admin = User::whereRaw('LOWER(email) = ?', [mb_strtolower($registration->email)])->first();
            $admin?->ongs()->syncWithoutDetaching([$ong->id => ['role' => 'admin']]);

            $registration->update(['status' => 'approved', 'ong_id' => $ong->id]);

            return $ong;
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'ong';
        $slug = $base;
        $suffix = 2;

        while (Ong::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
