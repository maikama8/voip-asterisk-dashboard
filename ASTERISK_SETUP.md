# Asterisk AMI Setup Guide

## What is Asterisk AMI?

Asterisk Manager Interface (AMI) is a system monitoring and management interface provided by Asterisk. It allows external programs to control and monitor the Asterisk PBX.

## Testing the Connection Feature

### Option 1: Use Demo/Test Credentials (Recommended for Testing)

You can test the connection feature with these common test values:

1. Go to Settings page in the dashboard
2. Enter these test credentials:
   - **Host**: `127.0.0.1` (or your Asterisk server IP)
   - **Port**: `5038`
   - **Username**: `admin`
   - **Secret**: `secret`

3. Click "Test Connection" to see if it connects

### Option 2: Install Asterisk Locally (For Real Testing)

#### On Ubuntu/Debian:

```bash
# Install Asterisk
sudo apt-get update
sudo apt-get install asterisk

# Start Asterisk
sudo systemctl start asterisk
sudo systemctl enable asterisk

# Check status
sudo systemctl status asterisk
```

#### Configure AMI:

1. Edit the AMI configuration:
```bash
sudo nano /etc/asterisk/manager.conf
```

2. Add this configuration:
```ini
[general]
enabled = yes
port = 5038
bindaddr = 0.0.0.0

[admin]
secret = secret
deny=0.0.0.0/0.0.0.0
permit=127.0.0.1/255.255.255.0
read = system,call,log,verbose,command,agent,user,config,dtmf,reporting,cdr,dialplan
write = system,call,log,verbose,command,agent,user,config,originate,reporting,cdr,dialplan
```

3. Reload Asterisk:
```bash
sudo asterisk -rx "manager reload"
```

#### Test Connection:

```bash
# Test if AMI is listening
telnet localhost 5038

# You should see:
# Asterisk Call Manager/X.X.X
```

### Option 3: Use Docker (Easiest for Testing)

```bash
# Run Asterisk in Docker
docker run -d \
  --name asterisk \
  -p 5038:5038 \
  -p 5060:5060/udp \
  andrius/asterisk

# Check if it's running
docker ps

# View logs
docker logs asterisk
```

Then configure AMI in the container:
```bash
docker exec -it asterisk bash
vi /etc/asterisk/manager.conf
# Add the configuration above
asterisk -rx "manager reload"
```

## What the Connection Test Does

When you click "Test Connection", the system:

1. **Connects to the AMI port** - Tests if the server is reachable
2. **Reads welcome message** - Verifies Asterisk is responding
3. **Attempts login** - Tests if credentials are correct
4. **Shows connection details** - Displays full connection information
5. **Updates status indicator** - Shows green (connected) or red (failed)

## Connection Status Indicators

- 🟡 **Yellow (Testing)** - Connection test in progress
- 🟢 **Green (Connected)** - Successfully connected and authenticated
- 🔴 **Red (Failed)** - Connection or authentication failed
- ⚪ **Gray (Not tested)** - No test performed yet

## Troubleshooting

### Connection Refused
- Check if Asterisk is running: `sudo systemctl status asterisk`
- Check if AMI port is open: `sudo netstat -tlnp | grep 5038`
- Check firewall: `sudo ufw status`

### Authentication Failed
- Verify credentials in `/etc/asterisk/manager.conf`
- Check permit/deny rules
- Reload manager: `sudo asterisk -rx "manager reload"`

### Timeout
- Check if host IP is correct
- Verify network connectivity: `ping <asterisk-host>`
- Check if port 5038 is accessible: `telnet <asterisk-host> 5038`

## Security Notes

⚠️ **Important Security Considerations:**

1. **Never expose AMI to the internet** without proper security
2. **Use strong passwords** in production
3. **Limit permit rules** to specific IP addresses
4. **Use firewall rules** to restrict access
5. **Enable TLS/SSL** for production environments

## Production Configuration Example

```ini
[general]
enabled = yes
port = 5038
bindaddr = 127.0.0.1  ; Only localhost
tlsenable=yes
tlsbindaddr=0.0.0.0:5039
tlscertfile=/etc/asterisk/keys/asterisk.pem
tlsprivatekey=/etc/asterisk/keys/asterisk.key

[admin]
secret = <strong-random-password>
deny=0.0.0.0/0.0.0.0
permit=192.168.1.100/255.255.255.255  ; Only your app server
read = system,call,log,verbose,command,agent,user
write = system,call,log,verbose,command,agent,user
```

## Testing Without Asterisk

If you don't have Asterisk installed, the connection test will show:
- Connection failed with error details
- This is expected and demonstrates the feature works
- You can still use the dashboard with seeded data

## Next Steps

After successful connection:
1. The dashboard will show real-time call data
2. SIP peers will be displayed
3. Call control features (hangup, transfer) will work
4. Queue statistics will be live

## Support

For Asterisk documentation:
- Official Docs: https://wiki.asterisk.org/
- AMI Documentation: https://wiki.asterisk.org/wiki/display/AST/Asterisk+Manager+Interface+AMI

For this application:
- Check the connection details in the Settings page
- View browser console for detailed error messages
- Check Laravel logs: `storage/logs/laravel.log`
