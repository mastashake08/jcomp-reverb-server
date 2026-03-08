# How Reverb Application Management Works

## The Problem You Identified

You asked: **"How is reverb knowing what applications are available to send to? Where is the config being updated?"**

Great question! By default, Reverb uses a **static configuration file** to define which applications can connect. This doesn't work well for a dynamic multi-application manager.

## The Solution: Custom Database Provider

We've implemented a **custom application provider** that makes Reverb read applications from your database instead of the config file.

### Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│ Your Dashboard (Create/Edit/Delete Applications)           │
└───────────────────┬─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────┐
│ ApplicationService                                          │
│ - Creates/Updates/Deletes apps in database                 │
│ - Clears Reverb cache after changes                        │
└───────────────────┬─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────┐
│ applications table (SQLite/MySQL)                           │
│ - Stores app_id, app_key, app_secret                       │
│ - Stores status, max_connections, etc.                     │
└───────────────────┬─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────┐
│ DatabaseApplicationProvider                                 │
│ - Reads active applications from database                   │
│ - Caches results for 5 minutes                             │
│ - Provides interface Reverb expects                        │
└───────────────────┬─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────┐
│ Laravel Reverb Server                                       │
│ - Authenticates incoming connections                        │
│ - Uses DatabaseApplicationProvider to verify credentials    │
└─────────────────────────────────────────────────────────────┘
```

## Key Components

### 1. DatabaseApplicationProvider
**Location:** `app/Broadcasting/Reverb/DatabaseApplicationProvider.php`

This class implements Laravel Reverb's `ApplicationProvider` interface and provides these methods:

- `findById()` - Find app by ID
- `findByKey()` - Find app by key (used for authentication)
- `findBySecret()` - Find app by secret
- `all()` - Get all active applications

**Caching:** Results are cached for 5 minutes to reduce database queries.

### 2. ReverbServiceProvider
**Location:** `app/Providers/ReverbServiceProvider.php`

Registers the `DatabaseApplicationProvider` as the implementation of Reverb's `ApplicationProvider` contract.

### 3. Configuration Update
**Location:** `config/reverb.php` (line 72)

```php
'provider' => env('REVERB_APP_PROVIDER', 'database'),
```

Changed from `'config'` to `'database'` (with environment override support).

### 4. ApplicationService Updates
**Location:** `app/Services/ApplicationService.php`

Now calls `clearReverbCache()` after:
- Creating an application
- Updating an application  
- Deleting an application

This ensures Reverb picks up changes within 5 minutes (or immediately on cache clear).

## How Authentication Works

When a client application tries to connect to Reverb:

1. **Client sends:** app_key + app_secret
2. **Reverb calls:** `DatabaseApplicationProvider::findByKey($key)`
3. **Provider checks:** Database for matching active application
4. **Reverb verifies:** Secret matches
5. **Connection:** Allowed if credentials match and app is active

## Configuration Flow

### .env Configuration
```bash
# Set provider to 'database' to use your Application model
REVERB_APP_PROVIDER=database

# These are for the manager itself (optional fallback)
REVERB_APP_ID=944858
REVERB_APP_KEY=ux7jefq0pye5v8djcydh
REVERB_APP_SECRET=elh61c6x99naeafqmaos
```

### When You Create an Application

1. User fills form → POST `/applications`
2. `ApplicationController::store()` → `ApplicationService::createApplication()`
3. Service generates credentials:
   - `app_id`: Random 6-digit number
   - `app_key`: Random 20-character string
   - `app_secret`: Random 40-character string (encrypted in DB)
4. Service saves to database
5. Service calls `clearReverbCache()` 
6. DatabaseApplicationProvider cache is cleared
7. Next Reverb authentication will read fresh data from DB

## Testing the Integration

### 1. Create an Application
```bash
# Via UI: http://localhost:8000/applications/create
# Or via Tinker:
php artisan tinker

>>> $app = \App\Services\ApplicationService::class;
>>> app($app)->createApplication([
    'name' => 'Test App',
    'url' => 'https://test.com',
    'is_enabled' => true
]);
```

### 2. View Active Applications
```bash
php artisan tinker

>>> $provider = app(\App\Broadcasting\Reverb\DatabaseApplicationProvider::class);
>>> $apps = $provider->all();
>>> print_r($apps);
```

### 3. Test Authentication
From a client app, configure:
```env
REVERB_APP_ID=<your-generated-id>
REVERB_APP_KEY=<your-generated-key>
REVERB_APP_SECRET=<your-generated-secret>
REVERB_HOST="localhost"
REVERB_PORT=8080
```

Start Reverb and the client should connect successfully.

## Cache Management

### Automatic Cache Clearing
The cache is automatically cleared when:
- An application is created
- An application is updated
- An application is deleted
- An application status changes

### Manual Cache Clearing
```bash
php artisan tinker

>>> $provider = app(\App\Broadcasting\Reverb\DatabaseApplicationProvider::class);
>>> $provider->clearCache();
```

Or via Artisan command:
```bash
php artisan cache:forget reverb.applications
```

## Troubleshooting

### Applications Not Connecting

**Problem:** Client can't connect to Reverb
**Check:**
1. Is the application marked as "active" and "enabled"?
   ```sql
   SELECT * FROM applications WHERE status='active' AND is_enabled=1;
   ```
2. Clear the cache:
   ```bash
   php artisan cache:forget reverb.applications
   ```
3. Check Reverb logs for authentication errors

### Provider Not Working

**Problem:** Reverb still uses config file
**Check:**
1. Verify `.env` has `REVERB_APP_PROVIDER=database`
2. Restart Reverb server:
   ```bash
   php artisan reverb:restart
   ```
3. Check `bootstrap/providers.php` includes `ReverbServiceProvider`

### Cache Not Clearing

**Problem:** Changes not reflected immediately
**Solution:** The cache lasts 5 minutes by default. To change:
```php
// In DatabaseApplicationProvider.php
protected int $cacheDuration = 60; // Change to 1 minute
```

## Security Considerations

1. **Encrypted Secrets:** App secrets are encrypted in the database using Laravel's `Crypt` facade
2. **Active Only:** Only applications with `status='active'` and `is_enabled=true` can connect
3. **Connection Limits:** Each app has a `max_connections` limit enforced by Reverb
4. **Origin Validation:** Can configure `allowed_origins` per application (currently set to `['*']`)

## Extending the System

### Add Custom Validation
```php
// In DatabaseApplicationProvider::getApplications()
->filter(function (Application $app) {
    // Add custom logic
    return $app->some_custom_check === true;
})
```

### Add Per-Application Origins
Update the migration to add:
```php
$table->json('allowed_origins')->nullable();
```

Then in `DatabaseApplicationProvider`:
```php
'allowed_origins' => $app->allowed_origins ?? ['*'],
```

### Add Rate Limiting
Update the migration:
```php
$table->integer('rate_limit_per_minute')->default(60);
```

Then use in provider:
```php
'rate_limit' => $app->rate_limit_per_minute,
```

## Summary

**Before:** Reverb read applications from `config/reverb.php` - static and unchangeable at runtime.

**After:** Reverb reads applications from your `applications` database table via `DatabaseApplicationProvider` - fully dynamic with caching for performance.

Every time you create, update, or delete an application through your dashboard, Reverb automatically knows about it (within 5 minutes due to cache, or immediately if cache is cleared).
