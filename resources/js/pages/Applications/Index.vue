<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

interface Application {
    id: number
    name: string
    slug: string
    description?: string
    url: string
    status: 'active' | 'inactive' | 'error' | 'maintenance'
    is_enabled: boolean
    last_seen_at?: string
    created_at: string
}

interface Statistics {
    total: number
    active: number
    inactive: number
    error: number
    maintenance: number
}

interface Props {
    applications: Application[]
    statistics: Statistics
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Applications', href: '/applications' },
]

const getStatusBadgeClass = (status: string): string => {
    const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
    const statusClasses = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        error: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        maintenance: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    }
    return `${baseClasses} ${statusClasses[status as keyof typeof statusClasses] || statusClasses.inactive}`
}

const deleteApplication = (id: number) => {
    if (confirm('Are you sure you want to delete this application?')) {
        router.delete(route('applications.destroy', id))
    }
}

const toggleStatus = (id: number) => {
    router.post(route('applications.toggle-status', id))
}
</script>

<template>
    <Head title="Applications" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Applications Manager
                </h2>
                <Link
                    :href="route('applications.create')"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    Add Application
                </Link>
            </div>

            <div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Apps</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            {{ statistics.total }}
                        </dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Active</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600 dark:text-green-400">
                            {{ statistics.active }}
                        </dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Inactive</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-600 dark:text-gray-400">
                            {{ statistics.inactive }}
                        </dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Error</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600 dark:text-red-400">
                            {{ statistics.error }}
                        </dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Maintenance</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-yellow-600 dark:text-yellow-400">
                            {{ statistics.maintenance }}
                        </dd>
                    </div>
                </div>

                <!-- Applications List -->
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="applications.length === 0" class="text-center py-12">
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No applications</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Get started by adding a new application to manage with Reverb.
                            </p>
                            <div class="mt-6">
                                <Link
                                    :href="route('applications.create')"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                >
                                    Add Application
                                </Link>
                            </div>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold sm:pl-0">
                                            Name
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold">
                                            URL
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold">
                                            Status
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold">
                                            Last Seen
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="app in applications" :key="app.id">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium sm:pl-0">
                                            <Link
                                                :href="route('applications.show', app.id)"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                            >
                                                {{ app.name }}
                                            </Link>
                                            <p v-if="app.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ app.description }}
                                            </p>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <a :href="app.url" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ app.url }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span :class="getStatusBadgeClass(app.status)">
                                                {{ app.status }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ app.last_seen_at || 'Never' }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                            <Link
                                                :href="route('applications.edit', app.id)"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-4"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="toggleStatus(app.id)"
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-4"
                                            >
                                                Toggle
                                            </button>
                                            <button
                                                @click="deleteApplication(app.id)"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
