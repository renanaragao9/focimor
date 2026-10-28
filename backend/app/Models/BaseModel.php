<?php

namespace App\Models;

use App\Services\Log\LogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class BaseModel extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return app(LogService::class)->modelOptions();
    }

    public function beforeActivityLogged(Activity $activity, string $event): void
    {
        $activity->causer_id = app(LogService::class)->causerId();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::preventLazyLoading(! app()->isProduction());

        static::handleLazyLoadingViolationUsing(function ($model, $relation) {
            $message = "Lazy load [{$relation}] on model [".get_class($model).'].';
            app(LogService::class)->warning($message, ['model' => $model::class, 'relation' => $relation]);
            throw new \Exception($message);
        });
    }

    public function resolveRouteBinding(mixed $value, mixed $field = null): ?Model
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->firstOr(function () {
            abort(404, 'Registro não encontrado.');
        });
    }
}
