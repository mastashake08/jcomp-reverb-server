import { onMounted, onUnmounted, ref } from 'vue'
import type { Application, ApplicationStatus } from '@/types/application'

interface StatusChangeEvent {
    application_id: number
    name: string
    status: ApplicationStatus
    previous_status: ApplicationStatus
    timestamp: string
}

/**
 * Composable for real-time application status updates via Reverb
 * 
 * @example
 * ```ts
 * const { applications, isConnected } = useApplicationStatus()
 * ```
 */
export function useApplicationStatus() {
    const applications = ref<Map<number, Application>>(new Map())
    const isConnected = ref(false)
    const error = ref<string | null>(null)

    const updateApplicationStatus = (event: StatusChangeEvent) => {
        const app = applications.value.get(event.application_id)
        if (app) {
            app.status = event.status
            applications.value.set(event.application_id, app)
        }
    }

    onMounted(() => {
        // Check if Echo is available
        if (typeof window.Echo === 'undefined') {
            error.value = 'Laravel Echo is not initialized. Please ensure Echo is configured.'
            console.warn('Laravel Echo is not available. Real-time updates will not work.')
            return
        }

        try {
            // Listen to the applications channel
            window.Echo.channel('applications')
                .listen('.application.status.changed', (event: StatusChangeEvent) => {
                    console.log('Application status changed:', event)
                    updateApplicationStatus(event)
                })
                .error((err: Error) => {
                    error.value = err.message
                    console.error('Channel error:', err)
                })

            isConnected.value = true
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to connect to WebSocket'
            console.error('Failed to connect to Echo:', err)
        }
    })

    onUnmounted(() => {
        // Leave the channel when component unmounts
        if (typeof window.Echo !== 'undefined') {
            window.Echo.leave('applications')
        }
    })

    return {
        applications,
        isConnected,
        error,
        updateApplicationStatus,
    }
}

/**
 * Composable for listening to a specific application's events
 * 
 * @param applicationId - The ID of the application to monitor
 * 
 * @example
 * ```ts
 * const { status, lastSeen } = useApplicationMonitor(1)
 * ```
 */
export function useApplicationMonitor(applicationId: number) {
    const status = ref<ApplicationStatus>('inactive')
    const lastSeen = ref<string | null>(null)
    const isConnected = ref(false)
    const error = ref<string | null>(null)

    onMounted(() => {
        if (typeof window.Echo === 'undefined') {
            error.value = 'Laravel Echo is not initialized'
            return
        }

        try {
            window.Echo.channel('applications')
                .listen('.application.status.changed', (event: StatusChangeEvent) => {
                    if (event.application_id === applicationId) {
                        status.value = event.status
                        lastSeen.value = event.timestamp
                    }
                })

            isConnected.value = true
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Connection failed'
        }
    })

    onUnmounted(() => {
        if (typeof window.Echo !== 'undefined') {
            window.Echo.leave('applications')
        }
    })

    return {
        status,
        lastSeen,
        isConnected,
        error,
    }
}
