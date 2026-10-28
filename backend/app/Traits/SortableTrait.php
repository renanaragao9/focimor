<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SortableTrait
{
    protected function applySorting(
        Builder $query,
        ?string $sort = null,
        string $direction = 'asc',
        string $default = 'created_at',
        array $allowed = [],
    ): Builder {
        if ($sort && in_array($sort, $allowed, true)) {
            return $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        }

        return $query->orderBy($default, $direction === 'asc' ? 'asc' : 'desc');
    }
}
