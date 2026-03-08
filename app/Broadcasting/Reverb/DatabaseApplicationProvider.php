<?php

namespace App\Broadcasting\Reverb;

use App\Models\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Laravel\Reverb\Contracts\ApplicationProvider;

class DatabaseApplicationProvider implements ApplicationProvider
{
    /**
     * Cache duration for application list (in seconds)
     */
    protected int $cacheDuration = 300; // 5 minutes

    /**
     * Find an application by ID.
     */
    public function findById(int|string $id): ?object
    {
        return $this->getApplications()->firstWhere('id', $id);
    }

    /**
     * Find an application by key.
     */
    public function findByKey(string $key): ?object
    {
        return $this->getApplications()->firstWhere('key', $key);
    }

    /**
     * Find an application by secret.
     */
    public function findBySecret(string $secret): ?object
    {
        $applications = $this->getApplications();

        return $applications->first(function ($app) use ($secret) {
            try {
                return $app->secret === $secret;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Get all active applications.
     */
    public function all(): array
    {
        return $this->getApplications()->all();
    }

    /**
     * Get applications from cache or database.
     */
    protected function getApplications()
    {
        return Cache::remember('reverb.applications', $this->cacheDuration, function () {
            return Application::active()
                ->get()
                ->map(function (Application $app) {
                    return (object) [
                        'id' => $app->app_id,
                        'key' => $app->app_key,
                        'secret' => $app->app_secret, // Already decrypted by accessor
                        'name' => $app->name,
                        'capacity' => $app->max_connections,
                        'options' => array_merge([
                            'host' => parse_url($app->url, PHP_URL_HOST),
                            'port' => config('reverb.servers.reverb.port', 8080),
                            'scheme' => config('reverb.servers.reverb.options.scheme', 'http'),
                        ], $app->metadata ?? []),
                        'allowed_origins' => ['*'], // You can make this configurable per app
                        'ping_interval' => 60,
                        'max_message_size' => 10_000,
                        'max_connections' => $app->max_connections,
                    ];
                });
        });
    }

    /**
     * Clear the applications cache.
     */
    public function clearCache(): void
    {
        Cache::forget('reverb.applications');
    }
}
