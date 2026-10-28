<?php

declare(strict_types=1);

namespace App\Services\Log;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Support\LogOptions;
use Throwable;

class LogService
{
    public const LEVELS = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

    public function debug(string $message, array $context = [], ?Model $subject = null): void
    {
        $this->run('debug', $message, $context, $subject);
    }

    public function info(string $message, array $context = [], ?Model $subject = null): void
    {
        $this->run('info', $message, $context, $subject);
    }

    public function warning(string $message, array $context = [], ?Model $subject = null): void
    {
        $this->run('warning', $message, $context, $subject);
    }

    public function error(string $message, array $context = [], ?Model $subject = null): void
    {
        $this->run('error', $message, $context, $subject);
    }

    public function run(string $level, string $message, array $context = [], ?Model $subject = null, string $logName = 'system'): void
    {
        try {
            $logger = activity($logName)
                ->event($level)
                ->causedBy(Auth::user())
                ->withProperties($this->normalize($context));

            if ($subject !== null) {
                $logger->performedOn($subject);
            }

            $logger->log($message);
        } catch (Throwable $exception) {
            error_log('LogService falhou: '.$exception->getMessage().' | '.$level.': '.$message);
        }
    }

    public function modelOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function causerId(): ?int
    {
        $id = Auth::id();

        return $id === null ? null : (int) $id;
    }

    private function normalize(array $context): array
    {
        return array_map(function (mixed $value): mixed {
            if ($value instanceof Throwable) {
                return [
                    'class' => $value::class,
                    'message' => $value->getMessage(),
                    'file' => $value->getFile().':'.$value->getLine(),
                ];
            }

            if (is_array($value)) {
                return $this->normalize($value);
            }

            if (is_scalar($value) || $value === null) {
                return $value;
            }

            return is_object($value) && method_exists($value, '__toString') ? (string) $value : get_debug_type($value);
        }, $context);
    }
}
