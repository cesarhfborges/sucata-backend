<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class StatusController extends Controller
{
    public function index(): JsonResponse
    {
        $errors = [];

        $checks = [
            'app'       => 'up',
            'database' => $this->checkDatabase($errors),
            'cache'    => $this->checkCache($errors),
            'queue'    => $this->checkQueue($errors),
            'disk'     => $this->checkDisk($errors),
            'memory'   => $this->checkMemory($errors),
            'env'      => $this->checkEnv($errors),
            'php'      => $this->checkPhp($errors),
        ];

        $serverStatus = collect($checks)->every(fn ($v) => $v === 'up')
            ? 'ok'
            : 'degraded';

        return response()->json([
            'name'      => config('app.name', 'Sucata'),
            'version'   => config('app.version', '1.0.0'),
            'php'       => phpversion(),
            'server'    => $serverStatus,
            'checks'    => $checks,
            'timestamp' => Carbon::now()->toIso8601String(),
            'errors'    => empty($errors) ? null : $errors,
        ], $serverStatus === 'ok' ? 200 : 503);
    }

    /* ============================
     | CHECKS
     |============================ */

    private function checkDatabase(array &$errors): string
    {
        try {
            DB::select('SELECT 1');
            return 'up';
        } catch (\Throwable $e) {
            $errors['database'] = $e->getMessage();
            return 'down';
        }
    }

    private function checkCache(array &$errors): string
    {
        try {
            $key = 'healthcheck_cache_test';
            Cache::put($key, 'ok', 5);

            if (Cache::get($key) !== 'ok') {
                throw new \RuntimeException('Cache read/write failed');
            }

            return 'up';
        } catch (\Throwable $e) {
            $errors['cache'] = $e->getMessage();
            return 'down';
        }
    }

    private function checkQueue(array &$errors): string
    {
        try {
            Queue::size();
            return 'up';
        } catch (\Throwable $e) {
            $errors['queue'] = $e->getMessage();
            return 'down';
        }
    }

    private function checkStorage(array &$errors): string
    {
        try {
            $disk = Storage::disk(config('filesystems.default'));
            $file = 'healthcheck.txt';

            $disk->put($file, 'ok');
            $content = $disk->get($file);
            $disk->delete($file);

            if ($content !== 'ok') {
                throw new \RuntimeException('Storage read/write failed');
            }

            return 'up';
        } catch (\Throwable $e) {
            $errors['storage'] = $e->getMessage();
            return 'down';
        }
    }

    private function checkDisk(array &$errors): string
    {
        try {
            $free = disk_free_space(base_path());
            $total = disk_total_space(base_path());

            if ($free === false || $total === false) {
                throw new \RuntimeException('Unable to read disk space');
            }

            $freePercent = ($free / $total) * 100;

            if ($freePercent < 5) {
                throw new \RuntimeException('Disk space critically low');
            }

            return 'up';
        } catch (\Throwable $e) {
            $errors['disk'] = $e->getMessage();
            return 'down';
        }
    }

    private function checkMemory(array &$errors): string
    {
        try {
            $usage = memory_get_usage(true);
            $limit = ini_get('memory_limit');

            if ($limit === '-1') {
                return 'up';
            }

            $limitBytes = $this->toBytes($limit);

            if (($usage / $limitBytes) > 0.9) {
                throw new \RuntimeException('High memory usage');
            }

            return 'up';
        } catch (\Throwable $e) {
            $errors['memory'] = $e->getMessage();
            return 'down';
        }
    }

    private function checkEnv(array &$errors): string
    {
        try {
            if (!app()->environment()) {
                throw new \RuntimeException('Environment not loaded');
            }

            if (!config('app.key')) {
                throw new \RuntimeException('APP_KEY missing');
            }

            return 'up';
        } catch (\Throwable $e) {
            $errors['env'] = $e->getMessage();
            return 'down';
        }
    }

    private function checkPhp(array &$errors): string
    {
        try {
            if (version_compare(PHP_VERSION, '8.1', '<')) {
                throw new \RuntimeException('PHP version unsupported');
            }

            return 'up';
        } catch (\Throwable $e) {
            $errors['php'] = $e->getMessage();
            return 'down';
        }
    }

    /* ============================
     | HELPERS
     |============================ */

    private function toBytes(string $value): int
    {
        $unit = strtolower(substr($value, -1));
        $bytes = (int) $value;

        return match ($unit) {
            'g' => $bytes * 1024 ** 3,
            'm' => $bytes * 1024 ** 2,
            'k' => $bytes * 1024,
            default => $bytes,
        };
    }
}
