<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add proper authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:applications,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'url' => ['required', 'url', 'max:255'],
            'health_check_url' => ['nullable', 'url', 'max:255'],
            'app_id' => ['nullable', 'string', 'max:255', 'unique:applications,app_id'],
            'app_key' => ['nullable', 'string', 'max:255'],
            'app_secret' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'error', 'maintenance'])],
            'is_enabled' => ['nullable', 'boolean'],
            'max_connections' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'url' => 'application URL',
            'health_check_url' => 'health check URL',
            'app_id' => 'application ID',
            'app_key' => 'application key',
            'app_secret' => 'application secret',
            'max_connections' => 'maximum connections',
        ];
    }
}
