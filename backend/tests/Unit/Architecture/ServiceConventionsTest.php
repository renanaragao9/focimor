<?php

declare(strict_types=1);

namespace Tests\Unit\Architecture;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ServiceConventionsTest extends TestCase
{
    private function files(string $directory, ?string $pattern = null): array
    {
        return collect(File::allFiles(base_path($directory)))
            ->filter(fn ($file) => $file->getExtension() === 'php' && ($pattern === null || preg_match($pattern, $file->getFilename())))
            ->map(fn ($file) => $file->getPathname())
            ->values()
            ->all();
    }

    public function test_array_values_are_only_read_in_import_assignments(): void
    {
        $violations = [];

        foreach (array_merge($this->files('app/Services'), $this->files('app/Http/Controllers')) as $path) {
            foreach (file($path) as $number => $line) {
                if (! preg_match('/\$(data|result)\[/', $line)) {
                    continue;
                }

                if (! preg_match('/^\s*(\$\w+|\[[^\]]+\]) = .*;\s*$/', $line)) {
                    $violations[] = str_replace(base_path().'/', '', $path).':'.($number + 1).' '.trim($line);
                }
            }
        }

        $this->assertSame([], $violations, "Importe o valor para uma variável antes de usar:\n".implode("\n", $violations));
    }

    public function test_index_services_import_in_the_standard_order(): void
    {
        $rank = fn (string $line): int => match (true) {
            str_starts_with($line, '$page =') => 0,
            str_starts_with($line, '$perPage =') => 1,
            str_starts_with($line, '$sort =') => 2,
            str_starts_with($line, '$direction =') => 3,
            str_starts_with($line, '$search =') => 4,
            str_contains($line, "\$data['filters']") => 5,
            default => 6,
        };

        $violations = [];

        foreach ($this->files('app/Services', '/^Index\w+Service\.php$/') as $path) {
            if (! preg_match('/public function run\([^)]*\)[^\n]*\n    \{\n(.*?)\n\n/s', file_get_contents($path), $block)) {
                continue;
            }

            $lines = array_map('trim', explode("\n", $block[1]));

            if (! str_contains($block[1], '$data')) {
                continue;
            }

            $ranks = array_map($rank, $lines);
            $sorted = $ranks;
            sort($sorted);

            if ($ranks !== $sorted) {
                $violations[] = str_replace(base_path().'/', '', $path);
            }
        }

        $this->assertSame([], $violations, "Ordem esperada: \$page, \$perPage, \$sort, \$direction, \$search, filtros, derivadas:\n".implode("\n", $violations));
    }

    public function test_index_services_that_paginate_import_per_page_and_page(): void
    {
        $violations = [];

        foreach ($this->files('app/Services', '/^Index\w+Service\.php$/') as $path) {
            $source = file_get_contents($path);

            if (str_contains($source, '->paginate(') && (! str_contains($source, "\$perPage = (int) (\$data['per_page']") || ! str_contains($source, "\$page = (int) (\$data['page']"))) {
                $violations[] = str_replace(base_path().'/', '', $path);
            }
        }

        $this->assertSame([], $violations);
    }
}
