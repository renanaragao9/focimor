<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateTemporaryUrlHelper
{
    public static function run(string $path, int $daysExpiration = 7): string
    {
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $expiration = Carbon::now($timezone)->addDays($daysExpiration);
        $diskName = (string) config('filesystems.media', 'public');
        $disk = Storage::disk($diskName);

        if ($disk->providesTemporaryUrls()) {
            return $disk->temporaryUrl($path, $expiration);
        }

        return $disk->url($path);
    }
}
