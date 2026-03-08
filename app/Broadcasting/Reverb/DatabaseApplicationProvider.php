<?php

namespace App\Broadcasting\Reverb;

use App\Models\Application as ApplicationModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Laravel\Reverb\Application;
use Laravel\Reverb\Contracts\ApplicationProvider;
use Laravel\Reverb\Exceptions\InvalidApplication;

class DatabaseApplicationProvider implements ApplicationProvider
{
    /**
     * Cache duration for application list (in seconds)
     */
    protected int $cacheDuration = 300; // 5 minutes

    /**
     * Find an application by ID.
     *
     * @throws \Laravel\Reverb\Exceptions\InvalidApplication
     */
    public function findById(string $id): Application
    {
        $app = $this->getApplications()->first(fn($application) => $application->id() === $id);

        if (! $app) {
            throw new InvalidApplication;
        }

        return $app;
    }

    /**
     * Find an application by key.
     *
     * @throws \Laravel\Reverb\Exceptions\InvalidApplication
     */
    public function findByKey(string $key): Application
    {
        $app = $this->getApplications()->first(fn($application) => $application->key() === $key);

        if (! $app) {
            throw new InvalidApplication;
        }

        return $app;
    }

    /**
     * Find an application by secret (helper method, not part of interface).
     */
    public function findBySecret(string $secret): ?Application
    {
        return $this->getApplications()->first(function ($app) use ($secret) {
            try {
                return $app->secret() === $secret;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Get all active applications.
     */
    public function all(): Collection
    {
        return $this->getApplications();
    }

    /**
     * Get applications from cache or database.
     *
     * @return \Illuminate\Support\Collection<\Laravel\Reverb\Application>
     */
    protected function getApplications(): Collection
    {
        return Cache::remember('reverb.applications', $this->cacheDuration, function () {
            return ApplicationModel::active()
                ->get()
                ->map(function (ApplicationModel $app) {
                    return new Application(
                        id: $app->app_id,
                        key: $app->app_key,
                        secret: $app->app_secret, // Already decrypted by accessor
                        pingInterval: 60,
                        activityTimeout: 30,
                        allowedOrigins: ['*'], // You can make this configurable per app
                        maxMessageSize: 10_000,
                        maxConnections: $app->max_connections,
                        acceptClientEventsFrom: 'all',
                        options: array_merge([
                            'host' => parse_url($app->url, PHP_URL_HOST),
                            'port' => config('reverb.servers.reverb.port', 8080),
                            'scheme' => config('reverb.servers.reverb.options.scheme', 'http'),
                        ], $app->metadata ?? []),
                    );
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
