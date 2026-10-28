<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\MyOng;

use App\Models\Ong;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateMyOngLogoService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, array $data): Ong
    {
        $logo = $data['logo'];

        $this->ensureOngAdmin($managedOng);

        return DB::transaction(function () use ($managedOng, $logo): Ong {
            $previous = $managedOng->logo_path;

            $managedOng->update(['logo_path' => $logo->store('ong-logos', config('filesystems.media'))]);

            if ($previous) {
                Storage::disk(config('filesystems.media'))->delete($previous);
            }

            return $managedOng->refresh();
        });
    }
}
