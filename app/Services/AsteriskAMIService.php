<?php

namespace App\Services;

use Exception;

class AsteriskAMIService
{
    private $socket;
    private string $host;
    private int $port;
    private string $username;
    private string $secret;

    public function __construct($host = null, $port = null, $username = null, $secret = null)
    {
        $this->host = $host ?? config('asterisk.ami.host', '127.0.0.1');
        $this->port = $port ?? config('asterisk.ami.port', 5038);
        $this->username = $username ?? config('asterisk.ami.username', 'admin');
        $this->secret = $secret ?? config('asterisk.ami.secret', 'secret');
    }

    public function connect(): bool
    {
        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, 10);
        
        if (!$this->socket) {
            return false;
        }

        $this->read();
        return $this->login();
    }

    private function login(): bool
    {
        $command = "Action: Login\r\n";
        $command .= "Username: {$this->username}\r\n";
        $command .= "Secret: {$this->secret}\r\n\r\n";
        
        fwrite($this->socket, $command);
        $response = $this->read();
        
        return str_contains($response, 'Success');
    }

    public function getActiveCalls(): array
    {
        $command = "Action: CoreShowChannels\r\n\r\n";
        fwrite($this->socket, $command);
        return $this->parseResponse($this->read());
    }

    public function getQueueStatus(string $queue = null): array
    {
        $command = "Action: QueueStatus\r\n";
        if ($queue) {
            $command .= "Queue: {$queue}\r\n";
        }
        $command .= "\r\n";
        
        fwrite($this->socket, $command);
        return $this->parseResponse($this->read());
    }

    public function hangup(string $channel): bool
    {
        $command = "Action: Hangup\r\n";
        $command .= "Channel: {$channel}\r\n\r\n";
        
        fwrite($this->socket, $command);
        $response = $this->read();
        
        return str_contains($response, 'Success');
    }

    public function redirect(string $channel, string $extension, string $context = 'default'): bool
    {
        $command = "Action: Redirect\r\n";
        $command .= "Channel: {$channel}\r\n";
        $command .= "Exten: {$extension}\r\n";
        $command .= "Context: {$context}\r\n";
        $command .= "Priority: 1\r\n\r\n";
        
        fwrite($this->socket, $command);
        $response = $this->read();
        
        return str_contains($response, 'Success');
    }

    private function read(): string
    {
        $response = '';
        while ($line = fgets($this->socket, 4096)) {
            $response .= $line;
            if (trim($line) === '') break;
        }
        return $response;
    }

    private function parseResponse(string $response): array
    {
        $lines = explode("\r\n", $response);
        $data = [];
        
        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $data[trim($key)] = trim($value);
            }
        }
        
        return $data;
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            fwrite($this->socket, "Action: Logoff\r\n\r\n");
            fclose($this->socket);
        }
    }

    public function sendCommand(string $command): string
    {
        if (!$this->socket) {
            throw new \Exception('Not connected to AMI');
        }

        fwrite($this->socket, $command);
        return $this->read();
    }

    public function getSystemInfo(): array
    {
        if (!$this->socket) {
            throw new \Exception('Not connected to AMI');
        }

        // Get Asterisk version and system info
        $command = "Action: Command\r\n";
        $command .= "Command: core show version\r\n\r\n";
        
        fwrite($this->socket, $command);
        $response = $this->read();
        
        $info = [
            'version' => 'Unknown',
            'uptime' => 'Unknown',
            'connected' => true
        ];

        // Parse version from response
        if (preg_match('/Asterisk ([\d\.]+)/', $response, $matches)) {
            $info['version'] = $matches[1];
        }

        // Get system uptime
        $command = "Action: Command\r\n";
        $command .= "Command: core show uptime\r\n\r\n";
        
        fwrite($this->socket, $command);
        $response = $this->read();
        
        if (preg_match('/System uptime: (.+)/', $response, $matches)) {
            $info['uptime'] = trim($matches[1]);
        }

        return $info;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
