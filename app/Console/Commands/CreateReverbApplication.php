<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CreateReverbApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reverb:create-app 
                            {--name= : The name of the application}
                            {--app-id= : The application ID}
                            {--app-key= : The application key}
                            {--app-secret= : The application secret}
                            {--url= : The application URL}
                            {--force : Force creation even if app with same credentials exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Reverb application in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?: $this->ask('Application name', 'Default Application');
        $appId = $this->option('app-id') ?: env('REVERB_APP_ID', (string) random_int(100000, 999999));
        $appKey = $this->option('app-key') ?: env('REVERB_APP_KEY', Str::lower(Str::random(20)));
        $appSecret = $this->option('app-secret') ?: env('REVERB_APP_SECRET', Str::random(40));
        $url = $this->option('url') ?: env('APP_URL', 'http://localhost');

        // Check if application with same app_id or app_key already exists
        $existingById = Application::where('app_id', $appId)->first();
        $existingByKey = Application::where('app_key', $appKey)->first();

        if (($existingById || $existingByKey) && !$this->option('force')) {
            $this->error('An application with these credentials already exists!');
            
            if ($existingById) {
                $this->line("  App ID '{$appId}' is used by: {$existingById->name}");
            }
            if ($existingByKey) {
                $this->line("  App Key '{$appKey}' is used by: {$existingByKey->name}");
            }
            
            $this->line('');
            $this->info('Use --force to update the existing application or choose different credentials.');
            return 1;
        }

        // Update existing or create new
        if ($existingById && $this->option('force')) {
            $application = $existingById;
            $application->update([
                'name' => $name,
                'slug' => Str::slug($name),
                'app_key' => $appKey,
                'app_secret' => $appSecret, // Will be encrypted by the accessor
                'url' => $url,
                'status' => 'active',
                'is_enabled' => true,
            ]);
            $action = 'updated';
        } else {
            $application = Application::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => 'Reverb WebSocket Application',
                'url' => $url,
                'app_id' => $appId,
                'app_key' => $appKey,
                'app_secret' => $appSecret, // Will be encrypted by the accessor
                'status' => 'active',
                'is_enabled' => true,
                'max_connections' => 100,
            ]);
            $action = 'created';
        }

        // Clear the cache
        \Cache::forget('reverb.applications');

        $this->info("Application {$action} successfully!");
        $this->line('');
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $application->id],
                ['Name', $application->name],
                ['App ID', $application->app_id],
                ['App Key', $application->app_key],
                ['App Secret', $application->app_secret],
                ['URL', $application->url],
                ['Status', $application->status],
                ['Enabled', $application->is_enabled ? 'Yes' : 'No'],
            ]
        );

        $this->line('');
        $this->info('Add these to your .env file:');
        $this->line("REVERB_APP_ID={$application->app_id}");
        $this->line("REVERB_APP_KEY={$application->app_key}");
        $this->line("REVERB_APP_SECRET={$application->app_secret}");

        return 0;
    }
}
