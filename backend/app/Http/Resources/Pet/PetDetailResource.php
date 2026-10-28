<?php

declare(strict_types=1);

namespace App\Http\Resources\Pet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'microchip' => $this->microchip,
            'pedigree_number' => $this->pedigree_number,
            'coat' => $this->coat,
            'blood_type' => $this->blood_type,
            'temperament' => $this->temperament,
            'allergies' => $this->allergies,
            'chronic_conditions' => $this->chronic_conditions,
            'diet' => $this->diet,
            'vet_name' => $this->vet_name,
            'vet_clinic' => $this->vet_clinic,
            'vet_phone' => $this->vet_phone,
            'notes' => $this->notes,
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
