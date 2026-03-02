# VoIP Call Center Dashboard

A modern, real-time web-based dashboard for monitoring and managing VoIP/call center operations with Asterisk integration.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![License](https://img.shields.io/badge/license-MIT-green)

## Features

### 🎯 Core Features
- **Real-time Dashboard** - Live call monitoring with auto-refresh
- **Call Management** - Hangup and transfer calls via Asterisk AMI
- **Agent Management** - Monitor agent status and performance
- **Queue Management** - Track call queues and wait times
- **Reports & Analytics** - Call history, daily stats, and performance metrics
- **User Management** - Role-based access (Admin, Supervisor, Agent)

### 🔌 Asterisk Integration
- **AMI Connection** - Real-time connection to Asterisk Manager Interface
- **Connection Testing** - Test and verify AMI credentials
- **SIP Peer Discovery** - View connected SIP devices
- **Live Status Monitoring** - Real connection status indicators

### 👥 User Roles
- **Admin** - Full access to all features
- **Supervisor** - Access to monitoring and reports
- **Agent** - Limited access to dashboard

## Screenshots

### Dashboard
Real-time monitoring with live statistics and active calls.

### Settings
Configure Asterisk AMI connection with real-time testing.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: SQLite (easily switchable to MySQL/PostgreSQL)
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **VoIP**: Asterisk AMI Integration
- **Authentication**: Laravel Sanctum

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM (for asset compilation)
- Asterisk server with AMI enabled (optional for development)

### Quick Start

1. **Clone the repository**
```bash
git clone <repository-url>
cd voip-call-center-dashboard
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
```bash
# SQLite (default)
touch database/database.sqlite

# Or configure MySQL/PostgreSQL in .env
```

5. **Run migrations and seed data**
```bash
php artisan migrate:fresh --seed
```

6. **Build assets**
```bash
npm run build
```

7. **Start the server**
```bash
php artisan serve
```

8. **Access the application**
```
http://localhost:8000
```

## Default Credentials

After seeding, you can login with:

- **Admin**: `admin@voip.local` / `password`
- **Agent 1**: `john@voip.local` / `password`
- **Agent 2**: `jane@voip.local` / `password`

## Asterisk Configuration

### Configure AMI in Asterisk

1. Edit `/etc/asterisk/manager.conf`:
```ini
[general]
enabled = yes
port = 5038
bindaddr = 0.0.0.0

[admin]
secret = secret
deny=0.0.0.0/0.0.0.0
permit=127.0.0.1/255.255.255.0
read = system,call,log,verbose,command,agent,user,config
write = system,call,log,verbose,command,agent,user,config
```

2. Reload Asterisk:
```bash
asterisk -rx "manager reload"
```

3. Configure in `.env`:
```env
ASTERISK_AMI_HOST=127.0.0.1
ASTERISK_AMI_PORT=5038
ASTERISK_AMI_USERNAME=admin
ASTERISK_AMI_SECRET=secret
```

4. Test connection in Settings page

### Docker Setup (Optional)

```bash
docker run -d \
  --name asterisk \
  -p 5038:5038 \
  -p 5060:5060/udp \
  andrius/asterisk
```

See [ASTERISK_SETUP.md](ASTERISK_SETUP.md) for detailed instructions.

## Development

### Running in Development Mode

```bash
# Start Laravel server
php artisan serve

# Watch for asset changes (in another terminal)
npm run dev
```

### Running Tests

```bash
php artisan test
```

### Code Style

```bash
# Fix code style
./vendor/bin/pint
```

## API Endpoints

### Dashboard
- `GET /api/dashboard/stats` - Overall statistics
- `GET /api/dashboard/active-calls` - Active calls list
- `GET /api/dashboard/agents` - Agent status
- `GET /api/dashboard/queues` - Queue information

### Call Management
- `POST /api/calls/hangup` - Hangup a call
- `POST /api/calls/transfer` - Transfer a call

### Reports
- `GET /api/reports/call-history` - Call history with filters
- `GET /api/reports/daily-stats` - Daily statistics

### Settings
- `POST /api/settings/test-connection` - Test AMI connection
- `POST /api/settings/asterisk` - Update AMI settings
- `GET /api/settings/sip-peers` - Get SIP peers

## Database Schema

### Users
- Stores user accounts with roles (admin, supervisor, agent)

### Agents
- Agent profiles linked to users with extension and SIP peer info

### Queues
- Call queues with Asterisk queue mapping

### Calls
- Call records with CDR data (caller ID, destination, status, duration)

## Features in Detail

### Real-time Dashboard
- Live call statistics (active, waiting, total)
- Online agent count
- Active calls list with status
- Agent status monitoring
- Auto-refresh every 5 seconds

### Call Management
- View active calls in real-time
- Hangup calls directly from dashboard
- Transfer calls to different extensions
- Call history with search and filters

### Reports & Analytics
- Daily call statistics
- Agent performance metrics
- Call history with date range filters
- Export capabilities (coming soon)

### Settings
- Asterisk AMI configuration
- Real-time connection testing
- SIP peer discovery
- System information display

## Security

- Environment variables for sensitive data
- CSRF protection on all forms
- Role-based access control
- Session-based authentication
- API authentication via Sanctum

## Troubleshooting

### Connection Issues

**AMI Connection Failed:**
- Check if Asterisk is running
- Verify AMI port (5038) is open
- Check firewall settings
- Verify credentials in manager.conf

**Database Errors:**
- Run `php artisan migrate:fresh --seed`
- Check database permissions
- Verify .env database configuration

**Asset Issues:**
- Run `npm install`
- Run `npm run build`
- Clear browser cache

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues and questions:
- Check [ASTERISK_SETUP.md](ASTERISK_SETUP.md) for Asterisk configuration
- Check [SIP_CONNECTION_FEATURE.md](SIP_CONNECTION_FEATURE.md) for connection details
- Open an issue on GitHub

## Roadmap

- [ ] WebSocket integration for real-time updates
- [ ] Call recording playback
- [ ] Advanced analytics and charts
- [ ] Browser notifications
- [ ] Call spy/monitor features
- [ ] CDR file parsing
- [ ] Multi-tenant support
- [ ] API documentation (Swagger)

## Credits

Built with Laravel, Tailwind CSS, and Alpine.js.

## Author

Created for VoIP call center management and monitoring.
