<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    private const FIRST_WEIGHTS = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    private const SECOND_WEIGHTS = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        if (! $this->isValid($digits)) {
            $fail('O CNPJ informado é inválido.');
        }
    }

    private function isValid(string $digits): bool
    {
        if (strlen($digits) !== 14 || preg_match('/^(\d)\1+$/', $digits) === 1) {
            return false;
        }

        $first = $this->checkDigit(substr($digits, 0, 12), self::FIRST_WEIGHTS);
        $second = $this->checkDigit(substr($digits, 0, 12).$first, self::SECOND_WEIGHTS);

        return substr($digits, 12) === $first.$second;
    }

    private function checkDigit(string $base, array $weights): int
    {
        $sum = 0;
        foreach ($weights as $index => $weight) {
            $sum += (int) $base[$index] * $weight;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
