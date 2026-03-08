export type ApplicationStatus = 'active' | 'inactive' | 'error' | 'maintenance'

export interface Application {
    id: number
    name: string
    slug: string
    description?: string
    url: string
    health_check_url?: string
    app_id: string
    app_key: string
    status: ApplicationStatus
    is_enabled: boolean
    max_connections: number
    last_seen_at?: string
    created_at: string
    updated_at: string
    metadata?: Record<string, any>
}

export interface ApplicationStatistics {
    total: number
    active: number
    inactive: number
    error: number
    maintenance: number
}

export interface ApplicationMetrics {
    connections: number
    messages_sent: number
    messages_received: number
    uptime: number
    response_time: number
}

export interface HealthCheckResult {
    status: 'healthy' | 'unhealthy' | 'unknown'
    message?: string
    response_time?: number
    checked_at?: string
}
