<?php

namespace App\Services;

use App\Broadcasting\Reverb\DatabaseApplicationProvider;
use App\Events\ApplicationStatusChanged;
use App\Models\Application;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Collection;

class ApplicationService
{
    /**
     * Get all applications with optional filtering.
     */
    public function getAllApplications(?string $status = null): Collection
    {
        $query = Application::query();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get a single application by ID.
     */
    public function getApplication(int $id): Application
    {
        return Application::findOrFail($id);
    }

    /**
     * Create a new application.
     */
    public function createApplication(array $data): Application
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['app_id'] = $data['app_id'] ?? $this->generateAppId();
        $data['app_key'] = $data['app_key'] ?? $this->generateAppKey();
        $data['app_secret'] = Crypt::encryptString($data['app_secret'] ?? $this->generateAppSecret());

        $application = Application::create($data);
        
        // Clear Reverb's application cache
        $this->clearReverbCache();
        
        return $application;
    }

    /**
     * Update an application.
     */
    public function updateApplication(Application $application, array $data): Application
    {
        // Handle slug generation if name changed
        if (isset($data['name']) && $data['name'] !== $application->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Encrypt secret if provided
        if (isset($data['app_secret'])) {
            $data['app_secret'] = Crypt::encryptString($data['app_secret']);
        }

        $application->update($data);
        
        // Clear Reverb's application cache
        $this->clearReverbCache();

        return $application->fresh();
    }

    /**
     * Delete an application.
     */
    public function deleteApplication(Application $application): bool
    {
        $deleted = $application->delete();
        
        // Clear Reverb's application cache
        if ($deleted) {
            $this->clearReverbCache();
        }
        
        return $deleted;
    }

    /**
     * Update application status.
     */
    public function updateStatus(Application $application, string $status): Application
    {
        $previousStatus = $application->status;
        
        $application->update([
            'status' => $status,
            'last_seen_at' => now(),
        ]);

        // Broadcast the status change
        event(new ApplicationStatusChanged(
            $application->fresh(),
            $previousStatus,
            $status
        ));

        return $application->fresh();
    }

    /**
     * Check application health.
     */
    public function checkHealth(Application $application): array
    {
        if (!$application->health_check_url) {
            return [
                'status' => 'unknown',
                'message' => 'No health check URL configured',
            ];
        }

        try {
            // TODO: Implement actual health check with HTTP client
            // For now, return a placeholder
            return [
                'status' => 'healthy',
                'response_time' => 0,
                'checked_at' => now(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a unique application ID.
     */
    protected function generateAppId(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * Generate a unique application key.
     */
    protected function generateAppKey(): string
    {
        return Str::lower(Str::random(20));
    }

    /**
     * Generate a secure application secret.
     */
    protected function generateAppSecret(): string
    {
        return Str::random(40);
    }

    /**
     * Get application statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => Application::count(),
            'active' => Application::where('status', 'active')->count(),
            'inactive' => Application::where('status', 'inactive')->count(),
            'error' => Application::where('status', 'error')->count(),
            'maintenance' => Application::where('status', 'maintenance')->count(),
        ];
    }
    
    /**
     * Clear Reverb's application cache.
     */
    protected function clearReverbCache(): void
    {
        try {
            $provider = app(DatabaseApplicationProvider::class);
            $provider->clearCache();
        } catch (\Exception $e) {
            // Log error but don't fail the operation
            \Log::warning('Failed to clear Reverb cache: ' . $e->getMessage());
        }
    }
}
