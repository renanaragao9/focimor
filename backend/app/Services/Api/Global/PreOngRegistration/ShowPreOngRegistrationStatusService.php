<?php

declare(strict_types=1);

namespace App\Services\Api\Global\PreOngRegistration;

use App\Models\PreOngRegistration;
use App\Services\BaseService;
use Illuminate\Validation\ValidationException;

class ShowPreOngRegistrationStatusService extends BaseService
{
    public function run(string $contact): PreOngRegistration
    {
        $contact = trim($contact);
        $query = PreOngRegistration::query();

        if (str_contains($contact, '@')) {
            if (! filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages(['contact' => ['Informe um e-mail válido.']]);
            }

            $query->whereRaw('LOWER(email) = ?', [mb_strtolower($contact)]);
        } else {
            $phone = preg_replace('/\D/', '', $contact) ?? '';

            if (strlen($phone) < 10 || strlen($phone) > 11) {
                throw ValidationException::withMessages(['contact' => ['Informe o telefone com DDD (10 ou 11 números).']]);
            }

            $query->where('phone', $phone);
        }

        $registration = $query->latest('id')->first();

        if (! $registration) {
            throw ValidationException::withMessages(['contact' => ['Não encontramos um pré-cadastro com esses dados.']]);
        }

        return $registration;
    }
}
