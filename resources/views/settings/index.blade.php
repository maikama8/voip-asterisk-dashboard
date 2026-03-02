@extends('layouts.app')

@section('page-title', 'Settings')
@section('page-description', 'System configuration')

@section('content')
<div class="space-y-6">
    <!-- Asterisk Settings -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold">Asterisk AMI Configuration</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">AMI Host</label>
                    <input type="text" value="{{ config('asterisk.ami.host') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">AMI Port</label>
                    <input type="text" value="{{ config('asterisk.ami.port') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">AMI Username</label>
                    <input type="text" value="{{ config('asterisk.ami.username') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                </div>
                <p class="text-sm text-gray-500">Edit .env file to change these settings</p>
            </div>
        </div>
    </div>

    <!-- General Settings -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold">General Settings</h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600">Additional settings - Coming soon</p>
        </div>
    </div>
</div>
@endsection
