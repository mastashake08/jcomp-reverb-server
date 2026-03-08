# Reverb Multiapplication Manager - Setup Guide

This application is a Laravel Reverb-powered multiapplication manager that allows you to monitor and manage multiple Laravel applications with real-time WebSocket communication and performance tracking via Laravel Pulse.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- SQLite (or MySQL/PostgreSQL)

## Installation Steps

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Install Laravel Echo for real-time features
npm install --save laravel-echo pusher-js
```

### 2. Configure Environment

Make sure your `.env` file has the following configurations:

```env
# Database (SQLite for development)
DB_CONNECTION=sqlite

# Reverb Configuration
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Reverb Configuration
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Pulse Configuration
PULSE_ENABLED=true
```

### 3. Set Up Database

```bash
# Create SQLite database if it doesn't exist
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### 4. Configure Laravel Echo (Frontend)

Update `resources/js/bootstrap.ts` or `resources/js/app.ts` to initialize Echo:

```typescript
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
})
```

Add type definitions in `resources/js/types/global.d.ts`:

```typescript
import Echo from 'laravel-echo'

declare global {
    interface Window {
        Echo: Echo
        Pusher: any
    }
}
```

### 5. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Services

You need to run three separate processes:

```bash
# Terminal 1: Laravel Development Server (or use Sail)
php artisan serve
# OR
sail up

# Terminal 2: Reverb WebSocket Server
php artisan reverb:start

# Terminal 3: Pulse Worker (Optional - for metrics)
php artisan pulse:work

# Terminal 4: Vite Dev Server
npm run dev
```

## Usage

### Adding Applications

1. Navigate to `/applications` in your browser
2. Click "Add Application"
3. Fill in the application details:
   - **Name**: Your application name
   - **URL**: The application's URL
   - **Health Check URL**: (Optional) Endpoint to monitor health
   - **Max Connections**: Maximum WebSocket connections allowed
4. Click "Create Application"
5. **Important**: Save the generated credentials (App ID, Key, Secret) - you'll need them to configure the client application

### Configuring Client Applications

In your client Laravel applications, add these configuration values:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=<the-generated-app-id>
REVERB_APP_KEY=<the-generated-app-key>
REVERB_APP_SECRET=<the-generated-app-secret>
REVERB_HOST="your-reverb-server.test"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Real-Time Status Updates

The dashboard automatically updates application statuses in real-time via WebSocket connections. When an application's status changes, all connected clients will see the update immediately.

### Monitoring with Pulse

Visit `/pulse` to view performance metrics:
- Request throughput
- Slow queries
- Exceptions
- Queue metrics
- Cache hit rates

## Architecture

### Backend Structure

```
app/
├── Events/
│   └── ApplicationStatusChanged.php    # Broadcasting event for status changes
├── Http/
│   ├── Controllers/
│   │   └── ApplicationController.php   # Thin controller delegating to service
│   └── Requests/
│       ├── StoreApplicationRequest.php  # Validation for creating apps
│       └── UpdateApplicationRequest.php # Validation for updating apps
├── Models/
│   └── Application.php                  # Application model with encrypted secrets
└── Services/
    └── ApplicationService.php           # Business logic for app management
```

### Frontend Structure

```
resources/js/
├── composables/
│   └── useApplicationStatus.ts          # Real-time status updates
├── pages/
│   └── Applications/
│       ├── Index.vue                    # List all applications
│       ├── Create.vue                   # Create new application
│       ├── Edit.vue                     # Edit application
│       └── Show.vue                     # View application details
└── types/
    └── application.ts                   # TypeScript interfaces
```

## Features

✅ **Application Management**
- Add, edit, delete applications
- Auto-generated credentials
- Health check monitoring
- Connection limits

✅ **Real-Time Updates**
- WebSocket-powered status changes
- Live application monitoring
- Instant notifications

✅ **Performance Monitoring**
- Laravel Pulse integration
- Custom metrics per application
- Historical data tracking

✅ **Security**
- Encrypted credentials storage
- Authentication required
- Two-factor auth support

## API Routes

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/applications` | List all applications |
| GET | `/applications/create` | Show create form |
| POST | `/applications` | Store new application |
| GET | `/applications/{id}` | Show application details |
| GET | `/applications/{id}/edit` | Show edit form |
| PUT | `/applications/{id}` | Update application |
| DELETE | `/applications/{id}` | Delete application |
| POST | `/applications/{id}/health` | Check application health |
| POST | `/applications/{id}/toggle-status` | Toggle active/inactive |

## Testing

```bash
# Run tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## Troubleshooting

### Reverb not connecting
- Check that `REVERB_*` environment variables are set correctly
- Ensure Reverb server is running: `php artisan reverb:start`
- Verify firewall rules allow connections on port 8080

### Real-time updates not working
- Make sure `laravel-echo` and `pusher-js` are installed
- Check browser console for WebSocket connection errors
- Verify Echo is initialized in your frontend bootstrap file

### Pulse not recording
- Ensure `PULSE_ENABLED=true` in `.env`
- Run the Pulse worker: `php artisan pulse:work`
- Check database tables were created: `pulse_*`

## Development Tips

- Use the `.instructions.md` file for Copilot guidance
- Follow the service pattern for new features
- Always broadcast significant events
- Write tests for critical paths
- Use TypeScript for type safety

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Build assets: `npm run build`
3. Optimize Laravel: `php artisan optimize`
4. Use a process manager (Supervisor) for Reverb and Pulse workers
5. Set up proper SSL certificates for WSS connections
6. Configure proper database (MySQL/PostgreSQL)

## Contributing

When adding new features:
1. Create service classes for business logic
2. Keep controllers thin
3. Broadcast events for state changes
4. Add TypeScript types
5. Write tests
6. Update documentation

## License

This project is open-sourced software licensed under the MIT license.
