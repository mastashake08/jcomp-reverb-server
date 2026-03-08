# Reverb Manager

**Reverb Manager** is a powerful Laravel-based application for managing multiple WebSocket applications through a centralized dashboard. Built on Laravel Reverb and enhanced with Laravel Pulse monitoring, it provides a complete solution for managing real-time application credentials, monitoring connections, and tracking performance metrics.

## 🚀 What is Reverb Manager?

Reverb Manager solves the challenge of managing multiple Laravel Reverb applications from a single control panel. Instead of manually configuring WebSocket credentials across different projects, Reverb Manager:

- **Centralized Application Management**: Create and manage multiple Reverb applications with unique credentials
- **Dynamic Configuration**: Applications are stored in a database and loaded dynamically (no config file editing)
- **Real-time Monitoring**: Track application status, connection counts, and health metrics
- **Secure Credentials**: Encrypted storage of application secrets with easy credential rotation
- **Performance Tracking**: Integrated Laravel Pulse for monitoring WebSocket performance
- **Type-Safe Routing**: Built with Laravel Wayfinder for fully type-safe route generation

## 🎯 Key Features

### Application Management
- Create, edit, and delete Reverb applications
- Auto-generate secure App IDs, Keys, and Secrets
- Configure max connections per application
- Enable/disable applications without deletion
- Soft delete support for application recovery

### Real-time Updates
- Live application status updates via WebSocket
- Broadcasting events for application state changes
- Automatic cache invalidation when applications are modified

### Security
- Encrypted application secrets in database
- Authentication required for all management actions
- Per-application access control
- Secure credential display with copy-to-clipboard functionality

### Developer Experience
- Vue 3 + TypeScript frontend with full type safety
- Service-oriented architecture in Laravel backend
- Comprehensive REST API for application management
- Detailed documentation and integration guides

## 📋 Prerequisites

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18+ with npm
- **Database**: SQLite (development) or MySQL/PostgreSQL (production)
- **Laravel**: 12.x

## 🔧 Installation

### 1. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies
npm install

# Laravel Echo for WebSocket client
npm install --save laravel-echo pusher-js
```

### 2. Environment Setup

Copy `.env.example` to `.env` and configure:

```env
# Database
DB_CONNECTION=sqlite

# Broadcasting
BROADCAST_CONNECTION=reverb
REVERB_APP_PROVIDER=database  # Use database instead of config file

# Reverb Server Configuration
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Configuration
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Pulse Monitoring
PULSE_ENABLED=true
```

### 3. Database Setup

```bash
# Create database
touch database/database.sqlite

# Run migrations
php artisan migrate

# (Optional) Seed test data
php artisan db:seed
```

### 4. Build Frontend Assets

```bash
# Development with hot reload
npm run dev

# Production build
npm run build
```

## 🚦 Running the Application

You need to start three services:

```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Reverb WebSocket server
php artisan reverb:start

# Terminal 3: Vite development server (if not already running)
npm run dev
```

Access the application at `http://localhost:8000`

## 📖 Usage

### Creating Your First Application

1. Log in to Reverb Manager
2. Navigate to **Applications** → **Create New**
3. Fill in application details:
   - **Name**: Your application name
   - **URL**: Your application URL
   - **Max Connections**: Maximum concurrent WebSocket connections
   - **Health Check URL** (optional): Endpoint for health monitoring
4. Click **Create Application**
5. Copy the generated credentials (App ID, Key, and Secret)

### Using Generated Credentials

In your client application, configure the Reverb credentials:

```env
REVERB_APP_ID=<generated-app-id>
REVERB_APP_KEY=<generated-app-key>
REVERB_APP_SECRET=<generated-app-secret>
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Managing Applications

- **View All**: Dashboard shows all applications with statistics
- **Edit**: Update application settings (credentials remain unchanged unless regenerated)
- **Toggle Status**: Enable/disable applications without deletion
- **Delete**: Soft delete applications (can be recovered)
- **View Details**: See full credentials and connection information

## 🏗️ Architecture

### Custom Database Provider

Reverb Manager implements a custom `DatabaseApplicationProvider` that replaces Laravel Reverb's default config-based provider. This allows applications to be:

- Stored in a database instead of config files
- Created and managed through a UI
- Updated without server restarts
- Cached for performance (5-minute TTL)

### Service Layer

The `ApplicationService` handles all business logic:
- Credential generation
- Application CRUD operations
- Cache invalidation
- Broadcasting state changes

### Frontend Architecture

- **Vue 3 Composition API**: Modern reactive components
- **TypeScript**: Full type safety across the codebase  
- **Inertia.js**: Seamless SPA experience with Laravel backend
- **Tailwind CSS 4**: Utility-first styling with dark mode support
- **Laravel Wayfinder**: Type-safe route generation

## 🔌 Integration Guide

### Connecting Your Laravel App

1. Install Laravel Reverb in your application:
```bash
composer require laravel/reverb
php artisan reverb:install
```

2. Configure with Reverb Manager credentials:
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=<from-reverb-manager>
REVERB_APP_KEY=<from-reverb-manager>
REVERB_APP_SECRET=<from-reverb-manager>
REVERB_HOST=<reverb-manager-host>
REVERB_PORT=8080
```

3. Your application will now authenticate through Reverb Manager

### Event Broadcasting

Reverb Manager broadcasts these events:
- `ApplicationStatusChanged`: When application status is toggled
- Cache clearing triggers after application modifications

## 📊 Monitoring with Pulse

Laravel Pulse integration provides:
- Real-time connection metrics
- Message throughput tracking
- Performance bottleneck identification
- Historical data analysis

Access Pulse dashboard at `/pulse`

## 🛠️ API Reference

### Application Endpoints

```
GET    /applications           # List all applications
GET    /applications/create    # Show create form
POST   /applications           # Store new application
GET    /applications/{id}      # Show application details
GET    /applications/{id}/edit # Show edit form
PUT    /applications/{id}      # Update application
DELETE /applications/{id}      # Soft delete application
POST   /applications/{id}/toggle-status  # Enable/disable
```

## 🔐 Security Best Practices

1. **Environment Variables**: Never commit `.env` files
2. **Secret Rotation**: Regularly regenerate application secrets
3. **Access Control**: Restrict dashboard access to authorized users
4. **HTTPS in Production**: Always use TLS for WebSocket connections
5. **Database Encryption**: Application secrets are encrypted at rest

## 📚 Additional Documentation

- [REVERB_INTEGRATION.md](REVERB_INTEGRATION.md): Deep dive into the custom provider architecture
- [.instructions.md](.instructions.md): Copilot AI instructions for contributing

## 🐛 Troubleshooting

### Applications Not Connecting

```bash
# Clear application cache
php artisan cache:forget reverb.applications

# Restart Reverb server
php artisan reverb:restart
```

### Database Provider Not Loading

Verify `.env` has:
```env
REVERB_APP_PROVIDER=database
```

### Frontend Build Errors

```bash
# Rebuild node_modules
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

## 🤝 Contributing

Contributions are welcome! Please read the contribution guidelines before submitting pull requests.

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 🙏 Credits

Built with:
- [Laravel 12](https://laravel.com)
- [Laravel Reverb](https://reverb.laravel.com)
- [Laravel Pulse](https://pulse.laravel.com)
- [Vue 3](https://vuejs.org)
- [Inertia.js](https://inertiajs.com)
- [Tailwind CSS](https://tailwindcss.com)

---

**Reverb Manager** - Simplifying WebSocket application management for Laravel developers.
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
