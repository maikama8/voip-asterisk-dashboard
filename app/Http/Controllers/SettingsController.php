<?php

namespace App\Http\Controllers;

use App\Services\AsteriskAMIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings');
    }

    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'secret' => 'required|string',
        ]);

        try {
            $ami = new AsteriskAMIService(
                $validated['host'],
                $validated['port'],
                $validated['username'],
                $validated['secret']
            );

            $connected = $ami->connect();

            if ($connected) {
                // Get some basic info to verify connection
                $info = $ami->getSystemInfo();
                $ami->disconnect();

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully connected to Asterisk AMI',
                    'info' => $info
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to Asterisk AMI. Please check your credentials.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveConnection(Request $request)
    {
        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'secret' => 'required|string',
        ]);

        // Test connection first
        try {
            $ami = new AsteriskAMIService(
                $validated['host'],
                $validated['port'],
                $validated['username'],
                $validated['secret']
            );

            if (!$ami->connect()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot save settings. Connection test failed.'
                ], 400);
            }
            $ami->disconnect();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }

        // Update .env file
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        $replacements = [
            'ASTERISK_AMI_HOST' => $validated['host'],
            'ASTERISK_AMI_PORT' => $validated['port'],
            'ASTERISK_AMI_USERNAME' => $validated['username'],
            'ASTERISK_AMI_SECRET' => $validated['secret'],
        ];

        foreach ($replacements as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);

        // Clear config cache
        \Artisan::call('config:clear');

        return response()->json([
            'success' => true,
            'message' => 'Asterisk AMI settings saved successfully'
        ]);
    }

    public function getConnectionStatus()
    {
        try {
            $ami = app(AsteriskAMIService::class);
            $connected = $ami->connect();
            
            if ($connected) {
                $info = $ami->getSystemInfo();
                $ami->disconnect();

                return response()->json([
                    'connected' => true,
                    'host' => config('asterisk.ami.host'),
                    'port' => config('asterisk.ami.port'),
                    'info' => $info
                ]);
            } else {
                return response()->json([
                    'connected' => false,
                    'host' => config('asterisk.ami.host'),
                    'port' => config('asterisk.ami.port'),
                    'message' => 'Not connected'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'connected' => false,
                'host' => config('asterisk.ami.host'),
                'port' => config('asterisk.ami.port'),
                'message' => $e->getMessage()
            ]);
        }
    }
}
