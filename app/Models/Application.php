<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Application extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'url',
        'health_check_url',
        'app_id',
        'app_key',
        'app_secret',
        'status',
        'last_seen_at',
        'metadata',
        'is_enabled',
        'max_connections',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_seen_at' => 'datetime',
        'metadata' => 'array',
        'is_enabled' => 'boolean',
        'max_connections' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'app_secret',
    ];

    /**
     * Get the decrypted app secret.
     */
    protected function appSecret(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn (?string $value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Get the decrypted secret for display purposes.
     */
    protected function decryptedSecret(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->app_secret,
        );
    }

    /**
     * Scope a query to only include active applications.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_enabled', true);
    }

    /**
     * Check if the application is online.
     */
    public function isOnline(): bool
    {
        return $this->status === 'active' && $this->is_enabled;
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'error' => 'red',
            'maintenance' => 'yellow',
            default => 'gray',
        };
    }
}
