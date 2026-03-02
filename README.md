# VoIP Call Center Dashboard

A web-based dashboard for monitoring and managing VoIP/call center operations with Asterisk integration.

## Features

- **Real-time Dashboard**: Live call monitoring, agent status, queue statistics
- **User Roles**: Admin, Supervisor, and Agent roles with different permissions
- **Call Controls**: Hangup, transfer calls via Asterisk AMI
- **Call History**: Search and filter call logs by date, agent, duration
- **Reports**: Daily call volume, agent performance metrics
- **Asterisk Integration**: Connects to Asterisk via AMI (Asterisk Manager Interface)

## Tech Stack

- **Backend**: Laravel 12 (PHP)
- **Database**: SQLite (easily switchable to MySQL/PostgreSQL)
- **Frontend**: Blade templates with Tailwind CSS
- **Real-time**: Auto-refresh dashboard (5-second intervals)
- **VoIP**: Asterisk AMI integration

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Asterisk server with AMI enabled (optional for development)

### Setup

1. Clone and install dependencies:
```bash
composer install
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure Asterisk AMI in `.env`:
```env
ASTERISK_AMI_HOST=127.0.0.1
ASTERISK_AMI_PORT=5038
ASTERISK_AMI_USERNAME=admin
ASTERISK_AMI_SECRET=secret
```

4. Run migrations and seed data:
```bash
php artisan migrate:fresh --seed
```

5. Start development server:
```bash
php artisan serve
```

6. Access dashboard at `http://localhost:8000`

## Default Credentials

- **Admin**: admin@voip.local / password
- **Agent 1**: john@voip.local / password
- **Agent 2**: jane@voip.local / password

## API Endpoints

### Dashboard
- `GET /api/dashboard/stats` - Overall statistics
- `GET /api/dashboard/active-calls` - List active calls
- `GET /api/dashboard/agents` - Agent status list
- `GET /api/dashboard/queues` - Queue information

### Call Controls
- `POST /api/calls/hangup` - Hangup a call
- `POST /api/calls/transfer` - Transfer a call

### Reports
- `GET /api/reports/call-history` - Call history with filters
- `GET /api/reports/daily-stats` - Daily statistics

## Database Schema

### Users
- Stores user accounts with roles (admin, supervisor, agent)

### Agents
- Agent profiles linked to users with extension, status, SIP peer

### Queues
- Call queues with Asterisk queue mapping

### Calls
- Call records with caller ID, destination, status, timestamps, duration

## Asterisk AMI Configuration

Add to `/etc/asterisk/manager.conf`:

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

## Development Mode (Without Asterisk)

The dashboard works without an Asterisk server using seeded data. AMI calls will fail gracefully, allowing you to develop and demo the UI.

## Next Steps

- [ ] Add Laravel Sanctum authentication
- [ ] Implement WebSocket for real-time updates (Laravel Reverb/Pusher)
- [ ] Add call recording playback
- [ ] Create agent performance charts (Chart.js)
- [ ] Add browser notifications for incoming calls
- [ ] Implement call spy/monitor features
- [ ] Add CDR file parsing for historical data
- [ ] Create supervisor dashboard with team metrics

## Project Structure

```
app/
├── Http/Controllers/Api/
│   ├── CallController.php       # Call control actions
│   ├── DashboardController.php  # Dashboard data
│   └── ReportController.php     # Reports and analytics
├── Models/
│   ├── User.php                 # User with roles
│   ├── Agent.php                # Agent profiles
│   ├── Queue.php                # Call queues
│   └── Call.php                 # Call records
└── Services/
    └── AsteriskAMIService.php   # AMI connection handler

database/migrations/              # Database schema
resources/views/dashboard.blade.php  # Main dashboard UI
routes/
├── api.php                      # API routes
└── web.php                      # Web routes
```

## License

MIT License
