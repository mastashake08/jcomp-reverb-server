<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import { home } from '@/routes';

// Connection state
const connectionStatus = ref<'disconnected' | 'connecting' | 'connected' | 'error'>('disconnected');
const connectionMessage = ref('Not connected');
const logs = ref<Array<{ time: string; type: 'info' | 'success' | 'error' | 'event'; message: string }>>([]);

// Test channel state
const testMessage = ref('');
const channelName = ref('test-channel');
const eventName = ref('TestEvent');
const isListening = ref(false);

// Configuration display
const config = ref({
    appKey: import.meta.env.VITE_REVERB_APP_KEY,
    host: import.meta.env.VITE_REVERB_HOST,
    port: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME,
});

const addLog = (type: 'info' | 'success' | 'error' | 'event', message: string) => {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour12: false });
    logs.value.unshift({ time, type, message });
    
    // Keep only last 50 logs
    if (logs.value.length > 50) {
        logs.value = logs.value.slice(0, 50);
    }
};

const testConnection = () => {
    try {
        connectionStatus.value = 'connecting';
        connectionMessage.value = 'Connecting to Reverb...';
        addLog('info', 'Attempting to connect to Reverb server...');

        if (typeof window.Echo === 'undefined') {
            throw new Error('Laravel Echo is not initialized');
        }

        // Check connection state
        const connector = (window.Echo as any).connector;
        if (connector && connector.pusher) {
            const state = connector.pusher.connection.state;
            addLog('info', `Pusher connection state: ${state}`);

            connector.pusher.connection.bind('connected', () => {
                connectionStatus.value = 'connected';
                connectionMessage.value = `Connected to ${config.value.host}:${config.value.port}`;
                addLog('success', 'Successfully connected to Reverb server!');
            });

            connector.pusher.connection.bind('disconnected', () => {
                connectionStatus.value = 'disconnected';
                connectionMessage.value = 'Disconnected from server';
                addLog('error', 'Disconnected from Reverb server');
            });

            connector.pusher.connection.bind('error', (err: any) => {
                connectionStatus.value = 'error';
                connectionMessage.value = err.error?.data?.message || 'Connection error';
                addLog('error', `Connection error: ${err.error?.data?.message || 'Unknown error'}`);
            });

            // Force reconnect if needed
            if (state !== 'connected') {
                connector.pusher.connect();
            } else {
                connectionStatus.value = 'connected';
                connectionMessage.value = `Connected to ${config.value.host}:${config.value.port}`;
                addLog('success', 'Already connected to Reverb server!');
            }
        }
    } catch (err) {
        connectionStatus.value = 'error';
        connectionMessage.value = err instanceof Error ? err.message : 'Unknown error';
        addLog('error', `Error: ${err instanceof Error ? err.message : 'Unknown error'}`);
    }
};

const subscribeToChannel = () => {
    if (!channelName.value) {
        addLog('error', 'Please enter a channel name');
        return;
    }

    try {
        addLog('info', `Subscribing to channel: ${channelName.value}`);
        
        const channel = window.Echo.channel(channelName.value);
        
        channel.subscribed(() => {
            isListening.value = true;
            addLog('success', `Subscribed to channel: ${channelName.value}`);
        });

        channel.error((err: any) => {
            addLog('error', `Channel error: ${err.message || 'Unknown error'}`);
        });

        if (eventName.value) {
            channel.listen(`.${eventName.value}`, (data: any) => {
                addLog('event', `Event received on .${eventName.value}: ${JSON.stringify(data)}`);
            });
            addLog('info', `Listening for events: .${eventName.value}`);
        }
    } catch (err) {
        addLog('error', `Subscribe error: ${err instanceof Error ? err.message : 'Unknown error'}`);
    }
};

const unsubscribeFromChannel = () => {
    if (!channelName.value) return;

    try {
        window.Echo.leave(channelName.value);
        isListening.value = false;
        addLog('info', `Unsubscribed from channel: ${channelName.value}`);
    } catch (err) {
        addLog('error', `Unsubscribe error: ${err instanceof Error ? err.message : 'Unknown error'}`);
    }
};

const clearLogs = () => {
    logs.value = [];
    addLog('info', 'Logs cleared');
};

onMounted(() => {
    addLog('info', 'Reverb Test page loaded');
    addLog('info', `Configuration: ${config.value.scheme}://${config.value.host}:${config.value.port}`);
    addLog('info', `App Key: ${config.value.appKey}`);
    
    // Auto-connect on mount
    setTimeout(() => testConnection(), 500);
});

onUnmounted(() => {
    if (isListening.value && channelName.value) {
        window.Echo.leave(channelName.value);
    }
});
</script>

<template>
    <Head title="Reverb Connection Test" />

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white">
        <!-- Navigation -->
        <nav class="border-b border-gray-700/50 backdrop-blur-sm bg-gray-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <Link :href="home()" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="text-xl font-bold bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent">
                                Reverb Manager
                            </span>
                        </Link>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-400">Connection Test</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent">
                    Reverb Connection Test
                </h1>
                <p class="text-gray-400">
                    Test your Laravel Reverb WebSocket connection using Laravel Echo
                </p>
            </div>

            <!-- Connection Status -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Status Card -->
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-lg border border-gray-700/50 p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Connection Status
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span
                                class="flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium"
                                :class="{
                                    'bg-green-500/20 text-green-400': connectionStatus === 'connected',
                                    'bg-yellow-500/20 text-yellow-400': connectionStatus === 'connecting',
                                    'bg-red-500/20 text-red-400': connectionStatus === 'error',
                                    'bg-gray-500/20 text-gray-400': connectionStatus === 'disconnected',
                                }"
                            >
                                <span
                                    class="w-2 h-2 rounded-full"
                                    :class="{
                                        'bg-green-400 animate-pulse': connectionStatus === 'connected',
                                        'bg-yellow-400 animate-pulse': connectionStatus === 'connecting',
                                        'bg-red-400': connectionStatus === 'error',
                                        'bg-gray-400': connectionStatus === 'disconnected',
                                    }"
                                ></span>
                                {{ connectionStatus }}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-400 bg-gray-900/50 rounded p-3">
                            {{ connectionMessage }}
                        </div>

                        <button
                            @click="testConnection"
                            class="w-full px-4 py-2 bg-gradient-to-r from-red-500 to-orange-600 hover:from-red-600 hover:to-orange-700 rounded-lg font-medium transition-all duration-200"
                        >
                            Test Connection
                        </button>
                    </div>
                </div>

                <!-- Configuration Card -->
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-lg border border-gray-700/50 p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Configuration
                    </h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">App Key:</span>
                            <code class="text-orange-400 bg-gray-900/50 px-2 py-1 rounded">{{ config.appKey }}</code>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Host:</span>
                            <code class="text-orange-400 bg-gray-900/50 px-2 py-1 rounded">{{ config.host }}</code>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Port:</span>
                            <code class="text-orange-400 bg-gray-900/50 px-2 py-1 rounded">{{ config.port }}</code>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Scheme:</span>
                            <code class="text-orange-400 bg-gray-900/50 px-2 py-1 rounded">{{ config.scheme }}</code>
                        </div>
                        <div class="pt-3 border-t border-gray-700/50">
                            <div class="text-gray-400 mb-1">WebSocket URL:</div>
                            <code class="text-xs text-green-400 bg-gray-900/50 px-2 py-1 rounded block break-all">
                                {{ config.scheme }}://{{ config.host }}:{{ config.port }}
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Channel Testing -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-lg border border-gray-700/50 p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Channel Subscription Test
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Channel Name</label>
                        <input
                            v-model="channelName"
                            type="text"
                            placeholder="test-channel"
                            class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500"
                            :disabled="isListening"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Event Name</label>
                        <input
                            v-model="eventName"
                            type="text"
                            placeholder="TestEvent"
                            class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500"
                            :disabled="isListening"
                        />
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        v-if="!isListening"
                        @click="subscribeToChannel"
                        :disabled="connectionStatus !== 'connected'"
                        class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Subscribe to Channel
                    </button>
                    <button
                        v-else
                        @click="unsubscribeFromChannel"
                        class="px-4 py-2 bg-gradient-to-r from-red-500 to-orange-600 hover:from-red-600 hover:to-orange-700 rounded-lg font-medium transition-all duration-200"
                    >
                        Unsubscribe from Channel
                    </button>
                    <span
                        v-if="isListening"
                        class="flex items-center gap-2 px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-medium"
                    >
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Listening
                    </span>
                </div>
            </div>

            <!-- Event Logs -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-lg border border-gray-700/50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Event Logs
                    </h2>
                    <button
                        @click="clearLogs"
                        class="px-3 py-1 text-sm bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        Clear Logs
                    </button>
                </div>

                <div class="bg-gray-900/50 rounded-lg p-4 font-mono text-sm max-h-96 overflow-y-auto">
                    <div v-if="logs.length === 0" class="text-gray-500 text-center py-8">
                        No logs yet. Test the connection to see events.
                    </div>
                    <div
                        v-for="(log, index) in logs"
                        :key="index"
                        class="mb-2 pb-2 border-b border-gray-800 last:border-b-0"
                        :class="{
                            'text-blue-400': log.type === 'info',
                            'text-green-400': log.type === 'success',
                            'text-red-400': log.type === 'error',
                            'text-purple-400': log.type === 'event',
                        }"
                    >
                        <span class="text-gray-500">[{{ log.time }}]</span>
                        <span class="ml-2 uppercase text-xs font-bold">{{ log.type }}</span>
                        <span class="ml-2">{{ log.message }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
