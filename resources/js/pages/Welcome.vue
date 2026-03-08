<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Reverb Manager - WebSocket Application Management" />
    
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white">
        <!-- Navigation -->
        <nav class="border-b border-gray-700/50 backdrop-blur-sm bg-gray-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent">
                            Reverb Manager
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="px-4 py-2 bg-gradient-to-r from-red-500 to-orange-600 hover:from-red-600 hover:to-orange-700 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="px-4 py-2 hover:bg-gray-800 rounded-lg font-medium transition-colors"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-orange-600 hover:from-red-600 hover:to-orange-700 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
            <div class="text-center mb-16">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-red-400 via-orange-400 to-red-500 bg-clip-text text-transparent">
                    Manage WebSocket Applications
                    <br />
                    with Confidence
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto mb-8">
                    Centralized Laravel Reverb application management. Create, monitor, and control multiple 
                    WebSocket applications from a single, powerful dashboard.
                </p>
                <div class="flex justify-center space-x-4">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="register()"
                        class="px-8 py-3 bg-gradient-to-r from-red-500 to-orange-600 hover:from-red-600 hover:to-orange-700 rounded-lg font-semibold text-lg transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5"
                    >
                        Get Started Free
                    </Link>
                    <Link
                        v-else
                        :href="dashboard()"
                        class="px-8 py-3 bg-gradient-to-r from-red-500 to-orange-600 hover:from-red-600 hover:to-orange-700 rounded-lg font-semibold text-lg transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5"
                    >
                        Go to Dashboard
                    </Link>
                    <a
                        href="https://github.com/laravel/reverb"
                        target="_blank"
                        class="px-8 py-3 bg-gray-800 hover:bg-gray-700 border border-gray-600 rounded-lg font-semibold text-lg transition-all duration-200"
                    >
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-3 gap-8 mb-20">
                <!-- Feature 1 -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:border-red-500/50 transition-colors">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Centralized Management</h3>
                    <p class="text-gray-400">
                        Create and manage multiple Reverb applications from one dashboard. No more config file juggling.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:border-orange-500/50 transition-colors">
                    <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Real-time Updates</h3>
                    <p class="text-gray-400">
                        Live application status, connection monitoring, and instant WebSocket event broadcasting.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:border-red-500/50 transition-colors">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Secure by Default</h3>
                    <p class="text-gray-400">
                        Encrypted credentials, auto-generated secrets, and secure credential rotation built-in.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:border-orange-500/50 transition-colors">
                    <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Performance Monitoring</h3>
                    <p class="text-gray-400">
                        Integrated Laravel Pulse for connection metrics, throughput tracking, and performance insights.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:border-red-500/50 transition-colors">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Developer Friendly</h3>
                    <p class="text-gray-400">
                        Type-safe routing with Wayfinder, Vue 3 + TypeScript, and comprehensive API documentation.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:border-orange-500/50 transition-colors">
                    <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Dynamic Configuration</h3>
                    <p class="text-gray-400">
                        Applications stored in database, loaded dynamically. Update credentials without server restarts.
                    </p>
                </div>
            </div>

            <!-- How It Works -->
            <div class="bg-gray-800/30 backdrop-blur-sm border border-gray-700 rounded-2xl p-8 md:p-12">
                <h2 class="text-3xl font-bold mb-8 text-center">How It Works</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                            1
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Create Application</h3>
                        <p class="text-gray-400">
                            Add a new application through the dashboard. Auto-generate secure credentials instantly.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                            2
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Configure Client</h3>
                        <p class="text-gray-400">
                            Copy credentials to your Laravel app's .env file. Connect and authenticate seamlessly.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                            3
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Monitor & Manage</h3>
                        <p class="text-gray-400">
                            Track connections, view metrics, and manage all applications from one central location.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tech Stack -->
            <div class="mt-20 text-center">
                <h2 class="text-2xl font-bold mb-8 text-gray-400">Built with Modern Technologies</h2>
                <div class="flex flex-wrap justify-center gap-6 text-gray-500">
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Laravel 12</span>
                    </div>
                    <span class="text-gray-700">•</span>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Laravel Reverb</span>
                    </div>
                    <span class="text-gray-700">•</span>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Laravel Pulse</span>
                    </div>
                    <span class="text-gray-700">•</span>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Vue 3</span>
                    </div>
                    <span class="text-gray-700">•</span>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">TypeScript</span>
                    </div>
                    <span class="text-gray-700">•</span>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Inertia.js</span>
                    </div>
                    <span class="text-gray-700">•</span>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Tailwind CSS</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="border-t border-gray-700/50 mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-gray-400">
                <p class="mb-2">
                    <strong class="text-white">Reverb Manager</strong> - Simplifying WebSocket application management for Laravel developers
                </p>
                <p class="text-sm">
                    Built with ❤️ using Laravel, Reverb, and Vue 3
                </p>
            </div>
        </footer>
    </div>
</template>
