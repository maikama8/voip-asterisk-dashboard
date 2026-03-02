@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Connection Status Card -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Asterisk AMI Connection Status</h3>
                <button onclick="checkConnection()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Check Connection
                </button>
            </div>
            
            <div id="connection-status" class="p-4 rounded-lg bg-gray-50">
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                    <span class="text-gray-600">Checking connection...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Asterisk Configuration -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Asterisk AMI Configuration</h3>
            
            <form id="ami-config-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">AMI Host</label>
                    <input type="text" id="ami-host" name="host" value="{{ config('asterisk.ami.host') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="127.0.0.1">
                    <p class="text-xs text-gray-500 mt-1">IP address or hostname of your Asterisk server</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">AMI Port</label>
                    <input type="number" id="ami-port" name="port" value="{{ config('asterisk.ami.port') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="5038">
                    <p class="text-xs text-gray-500 mt-1">Default AMI port is 5038</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">AMI Username</label>
                    <input type="text" id="ami-username" name="username" value="{{ config('asterisk.ami.username') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="admin">
                    <p class="text-xs text-gray-500 mt-1">Username configured in manager.conf</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">AMI Secret</label>
                    <input type="password" id="ami-secret" name="secret" value="{{ config('asterisk.ami.secret') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="secret">
                    <p class="text-xs text-gray-500 mt-1">Password configured in manager.conf</p>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="testConnection()" 
                            class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        Test Connection
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Save Settings
                    </button>
                </div>
            </form>

            <div id="test-result" class="mt-4 hidden"></div>
        </div>
    </div>

    <!-- System Information -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">System Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Laravel:</span>
                    <span class="font-medium">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">PHP:</span>
                    <span class="font-medium">{{ PHP_VERSION }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Database:</span>
                    <span class="font-medium">{{ config('database.default') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Environment:</span>
                    <span class="font-medium">{{ app()->environment() }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <button onclick="clearCache()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 transition text-left">
                    Clear Cache
                </button>
                <button onclick="window.location.reload()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 transition text-left">
                    Reload Page
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Check connection status on page load
    window.addEventListener('load', checkConnection);

    async function checkConnection() {
        const statusDiv = document.getElementById('connection-status');
        statusDiv.innerHTML = `
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-gray-600">Checking connection...</span>
            </div>
        `;

        try {
            const response = await fetch('/api/settings/connection-status', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.connected) {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                            <div>
                                <p class="font-semibold text-green-700">Connected to Asterisk</p>
                                <p class="text-sm text-gray-600">${data.host}:${data.port}</p>
                                ${data.info ? `
                                    <p class="text-xs text-gray-500 mt-1">Version: ${data.info.version || 'Unknown'}</p>
                                    ${data.info.uptime ? `<p class="text-xs text-gray-500">Uptime: ${data.info.uptime}</p>` : ''}
                                ` : ''}
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                `;
            } else {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <div>
                                <p class="font-semibold text-red-700">Not Connected</p>
                                <p class="text-sm text-gray-600">${data.host}:${data.port}</p>
                                <p class="text-xs text-red-600 mt-1">${data.message || 'Unable to connect to Asterisk AMI'}</p>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error checking connection:', error);
            statusDiv.innerHTML = `
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                    <div>
                        <p class="font-semibold text-red-700">Error</p>
                        <p class="text-sm text-gray-600">Failed to check connection status</p>
                    </div>
                </div>
            `;
        }
    }

    async function testConnection() {
        const resultDiv = document.getElementById('test-result');
        const formData = new FormData(document.getElementById('ami-config-form'));
        
        resultDiv.className = 'mt-4 p-4 rounded-lg bg-blue-50 border border-blue-200';
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = `
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-blue-700">Testing connection...</span>
            </div>
        `;

        try {
            const response = await fetch('/api/settings/test-connection', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                resultDiv.className = 'mt-4 p-4 rounded-lg bg-green-50 border border-green-200';
                resultDiv.innerHTML = `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-700">${data.message}</p>
                            ${data.info ? `
                                <p class="text-sm text-green-600 mt-1">Asterisk Version: ${data.info.version || 'Unknown'}</p>
                                ${data.info.uptime ? `<p class="text-sm text-green-600">Uptime: ${data.info.uptime}</p>` : ''}
                            ` : ''}
                        </div>
                    </div>
                `;
                checkConnection();
            } else {
                resultDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200';
                resultDiv.innerHTML = `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-700">Connection Failed</p>
                            <p class="text-sm text-red-600 mt-1">${data.message}</p>
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error testing connection:', error);
            resultDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200';
            resultDiv.innerHTML = `
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-700">Error</p>
                        <p class="text-sm text-red-600 mt-1">Failed to test connection</p>
                    </div>
                </div>
            `;
        }
    }

    document.getElementById('ami-config-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const resultDiv = document.getElementById('test-result');
        const formData = new FormData(this);
        
        resultDiv.className = 'mt-4 p-4 rounded-lg bg-blue-50 border border-blue-200';
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = `
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-blue-700">Saving settings...</span>
            </div>
        `;

        try {
            const response = await fetch('/api/settings/save-connection', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                resultDiv.className = 'mt-4 p-4 rounded-lg bg-green-50 border border-green-200';
                resultDiv.innerHTML = `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-700">${data.message}</p>
                            <p class="text-sm text-green-600 mt-1">Page will reload in 2 seconds...</p>
                        </div>
                    </div>
                `;
                setTimeout(() => window.location.reload(), 2000);
            } else {
                resultDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200';
                resultDiv.innerHTML = `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-700">Save Failed</p>
                            <p class="text-sm text-red-600 mt-1">${data.message}</p>
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            resultDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200';
            resultDiv.innerHTML = `
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-700">Error</p>
                        <p class="text-sm text-red-600 mt-1">Failed to save settings</p>
                    </div>
                </div>
            `;
        }
    });

    function clearCache() {
        alert('Cache clearing requires server-side action. Please run: php artisan cache:clear');
    }
</script>
@endpush
