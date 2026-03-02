# VoIP Call Center Dashboard

A modern, real-time call center dashboard with Asterisk AMI integration built with Laravel 12 and Tailwind CSS.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)

## 🌟 Features

### Real-time Monitoring
- **Live Dashboard** - Real-time call statistics and agent status
- **Active Calls** - Monitor ongoing calls with duration tracking
- **Agent Status** - Track agent availability and performance
- **Queue Management** - Monitor call queues and wait times

### Asterisk Integration
- **AMI Connection** - Direct integration with Asterisk Manager Interface
- **Connection Testing** - Test and verify AMI connectivity in real-time
- **SIP Peer Discovery** - Automatically detect and display SIP devices
- **Call Control** - Hangup and transfer calls directly from the dashboard

### Management Features
- **Agent Management** - Add, edit, and monitor call center agents
- **Queue Configuration** - Manage call queues and routing
- **User Management** - Role-based access control (Admin, Supervisor, Agent)
- **Call History** - Searchable call logs with filtering
- **Reports & Analytics** - Daily statistics and performance metrics

### User Interface
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Real-time Updates** - Auto-refresh every 5 seconds
- **Visual Indicators** - Color-coded status badges
- **Toast Notifications** - User-friendly feedback messages

## 📸 Screenshots

### Dashboard
Real-time overview of call center operations with live statistics.

### Settings
Configure Asterisk AMI connection with live testing and validation.

### Agent Management
Monitor and manage call center agents with status tracking.

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM (for asset compilation)
- Asterisk server with AMI enabled (optional for development)

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/maikama8/voip-asterisk-dashboard.git
cd voip-asterisk-dashboard
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Asterisk AMI** (edit `.env`)
```env
ASTERISK_AMI_HOST=127.0.0.1
ASTERISK_AMI_PORT=5038
ASTERISK_AMI_USERNAME=admin
ASTERISK_AMI_SECRET=secret
```

5. **Setup database**
```bash
touch database/database.sqlite
php artisan migrate --seed
```

6. **Build assets**
```bash
npm run build
```

7. **Start the server**
```bash
php artisan serve
```

8. **Access the dashboard**
```
http://localhost:8000
```

### Default Credentials

- **Admin**: admin@voip.local / password
- **Agent 1**: john@voip.local / password
- **Agent 2**: jane@voip.local / password

## 🔧 Configuration

### Asterisk AMI Setup

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

3. Test connection in Settings page

For detailed setup instructions, see [ASTERISK_SETUP.md](ASTERISK_SETUP.md)

## 📚 Documentation

- [Asterisk Setup Guide](ASTERISK_SETUP.md) - Complete Asterisk installation and configuration
- [SIP Connection Feature](SIP_CONNECTION_FEATURE.md) - Detailed feature documentation
- [API Documentation](#api-endpoints) - API endpoint reference

## 🎯 Usage

### Testing Connection

1. Navigate to **Settings** in the sidebar
2. Enter your Asterisk AMI credentials
3. Click **Test Connection**
4. View connection status and details
5. Click **Save Settings** to persist configuration

### Monitoring Calls

1. Go to **Dashboard** for real-time overview
2. View **Active Calls** for detailed call information
3. Use **Call History** to search past calls
4. Check **Reports** for analytics

### Managing Agents

1. Navigate to **Agents** (Admin only)
2. View agent status and performance
3. Add or edit agent profiles
4. Monitor agent availability

## 🔌 API Endpoints

### Dashboard
- `GET /api/dashboard/stats` - Overall statistics
- `GET /api/dashboard/active-calls` - List active calls
- `GET /api/dashboard/agents` - Agent status list
- `GET /api/dashboard/queues` - Queue information

### Call Control
- `POST /api/calls/hangup` - Hangup a call
- `POST /api/calls/transfer` - Transfer a call

### Reports
- `GET /api/reports/call-history` - Call history with filters
- `GET /api/reports/daily-stats` - Daily statistics

### Settings
- `POST /api/settings/test-connection` - Test AMI connection
- `POST /api/settings/asterisk` - Update AMI settings
- `GET /api/settings/sip-peers` - Get SIP peer list

## 🏗️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: SQLite (easily switchable to MySQL/PostgreSQL)
- **Real-time**: Auto-refresh with AJAX
- **VoIP**: Asterisk AMI integration

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── Api/
│   │   ├── CallController.php       # Call control actions
│   │   ├── DashboardController.php  # Dashboard data
│   │   └── ReportController.php     # Reports and analytics
│   ├── AuthController.php           # Authentication
│   └── SettingsController.php       # Settings management
├── Models/
│   ├── User.php                     # User with roles
│   ├── Agent.php                    # Agent profiles
│   ├── Queue.php                    # Call queues
│   └── Call.php                     # Call records
└── Services/
    └── AsteriskAMIService.php       # AMI connection handler

resources/views/
├── layouts/
│   └── app.blade.php                # Main layout
├── dashboard.blade.php              # Dashboard view
├── settings.blade.php               # Settings page
├── agents/                          # Agent management
├── calls/                           # Call views
├── queues/                          # Queue management
└── reports/                         # Reports and analytics
```

## 🔐 Security

- Environment variables for sensitive data
- Role-based access control
- CSRF protection
- Session-based authentication
- Input validation and sanitization

**Production Recommendations:**
- Use strong passwords
- Enable HTTPS/TLS
- Restrict AMI access by IP
- Regular security updates
- Database backups

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel Framework
- Asterisk PBX
- Tailwind CSS
- All contributors and users

## 📧 Contact

**Author**: Abdulrasheed  
**GitHub**: [@maikama8](https://github.com/maikama8)  
**Project Link**: [https://github.com/maikama8/voip-asterisk-dashboard](https://github.com/maikama8/voip-asterisk-dashboard)

## 🗺️ Roadmap

- [ ] WebSocket integration for real-time updates
- [ ] Call recording playback
- [ ] Advanced analytics and charts
- [ ] Browser notifications
- [ ] Call spy/monitor features
- [ ] CDR file parsing
- [ ] Multi-tenant support
- [ ] REST API documentation
- [ ] Docker deployment
- [ ] Kubernetes support

## ⚠️ Development Mode

The dashboard works without an Asterisk server using seeded data. AMI calls will fail gracefully, allowing you to develop and demo the UI without a live PBX system.

---

Made with ❤️ for the VoIP community
