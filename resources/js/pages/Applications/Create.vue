<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import * as ApplicationRoutes from '@/routes/applications'

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Applications', href: '/applications' },
    { title: 'Create', href: '/applications/create' },
]

const form = useForm({
    name: '',
    description: '',
    url: '',
    health_check_url: '',
    max_connections: 100,
    is_enabled: true,
})

const submit = () => {
    form.post(ApplicationRoutes.store.url(), {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Create Application" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Add New Application
                </h2>
                <Link
                    :href="ApplicationRoutes.index.url()"
                    class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                >
                    Back to Applications
                </Link>
            </div>

            <div>
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Application Name *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                placeholder="My Laravel App"
                            />
                            <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                placeholder="Brief description of the application"
                            />
                            <div v-if="form.errors.description" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- URL -->
                        <div>
                            <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Application URL *
                            </label>
                            <input
                                id="url"
                                v-model="form.url"
                                type="url"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                placeholder="https://myapp.com"
                            />
                            <div v-if="form.errors.url" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.url }}
                            </div>
                        </div>

                        <!-- Health Check URL -->
                        <div>
                            <label for="health_check_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Health Check URL
                            </label>
                            <input
                                id="health_check_url"
                                v-model="form.health_check_url"
                                type="url"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                placeholder="https://myapp.com/health"
                            />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Optional endpoint to monitor application health
                            </p>
                            <div v-if="form.errors.health_check_url" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.health_check_url }}
                            </div>
                        </div>

                        <!-- Max Connections -->
                        <div>
                            <label for="max_connections" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Maximum Connections
                            </label>
                            <input
                                id="max_connections"
                                v-model.number="form.max_connections"
                                type="number"
                                min="1"
                                max="10000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                            />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Maximum allowed WebSocket connections (default: 100)
                            </p>
                            <div v-if="form.errors.max_connections" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.max_connections }}
                            </div>
                        </div>

                        <!-- Is Enabled -->
                        <div class="flex items-center">
                            <input
                                id="is_enabled"
                                v-model="form.is_enabled"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900"
                            />
                            <label for="is_enabled" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                Enable this application immediately
                            </label>
                        </div>

                        <!-- Credentials Info -->
                        <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700 dark:text-blue-200">
                                        Application credentials (App ID, Key, and Secret) will be generated automatically.
                                        You'll receive them after creating the application.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="ApplicationRoutes.index.url()"
                                class="text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 dark:focus:ring-offset-gray-800"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Application</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
