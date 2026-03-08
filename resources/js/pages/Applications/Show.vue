<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import * as ApplicationRoutes from '@/routes/applications'

interface Application {
    id: number
    name: string
    slug: string
    description?: string
    url: string
    health_check_url?: string
    app_id: string
    app_key: string
    status: 'active' | 'inactive' | 'error' | 'maintenance'
    is_enabled: boolean
    max_connections: number
    last_seen_at?: string
    created_at: string
    updated_at: string
}

interface Props {
    application: Application
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Applications', href: '/applications' },
    { title: props.application.name, href: `/applications/${props.application.id}` },
]

const getStatusBadgeClass = (status: string): string => {
    const baseClasses = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium'
    const statusClasses = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        error: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        maintenance: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    }
    return `${baseClasses} ${statusClasses[status as keyof typeof statusClasses] || statusClasses.inactive}`
}

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text)
}

const deleteApplication = () => {
    if (confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
        router.delete(ApplicationRoutes.destroy.url({ application: props.application.id }))
    }
}

const toggleStatus = () => {
    router.post(`/applications/${props.application.id}/toggle-status`)
}
</script>

<template>
    <Head :title="application.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ application.name }}
                </h2>
                <div class="flex items-center gap-3">
                    <Link
                        :href="ApplicationRoutes.edit.url({ application: application.id })"
                        class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        Edit
                    </Link>
                    <Link
                        :href="ApplicationRoutes.index.url()"
                        class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        Back to Applications
                    </Link>
                </div>
            </div>

            <!-- Application Details -->
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Application Details
                            </h3>
                            <span :class="getStatusBadgeClass(application.status)">
                                {{ application.status }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ application.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ application.slug }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ application.description || 'No description provided' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">URL</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    <a :href="application.url" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ application.url }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Health Check URL</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    <a v-if="application.health_check_url" :href="application.health_check_url" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ application.health_check_url }}
                                    </a>
                                    <span v-else class="text-gray-500 dark:text-gray-400">Not configured</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Connections</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ application.max_connections }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Enabled</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ application.is_enabled ? 'Yes' : 'No' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Seen</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ application.last_seen_at || 'Never' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ application.created_at }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Credentials -->
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Reverb Credentials
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">App ID</label>
                                <div class="mt-1 flex items-center gap-2">
                                    <input
                                        type="text"
                                        :value="application.app_id"
                                        readonly
                                        class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                    />
                                    <button
                                        @click="copyToClipboard(application.app_id)"
                                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-600"
                                    >
                                        Copy
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">App Key</label>
                                <div class="mt-1 flex items-center gap-2">
                                    <input
                                        type="text"
                                        :value="application.app_key"
                                        readonly
                                        class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                                    />
                                    <button
                                        @click="copyToClipboard(application.app_key)"
                                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-600"
                                    >
                                        Copy
                                    </button>
                                </div>
                            </div>
                            <div class="rounded-md bg-yellow-50 p-4 dark:bg-yellow-900/20">
                                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                    <strong>Note:</strong> The App Secret is encrypted and cannot be displayed. Store it securely when first created.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Actions
                        </h3>
                        <div class="flex items-center gap-4">
                            <button
                                @click="toggleStatus"
                                class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                            >
                                Toggle Status
                            </button>
                            <button
                                @click="deleteApplication"
                                class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                            >
                                Delete Application
                            </button>
                        </div>
                    </div>
                </div>
        </div>
    </AppLayout>
</template>
