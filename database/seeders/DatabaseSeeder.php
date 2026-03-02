<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agent;
use App\Models\Queue;
use App\Models\Call;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@voip.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create agents
        $agent1 = User::create([
            'name' => 'John Agent',
            'email' => 'john@voip.local',
            'password' => Hash::make('password'),
            'role' => 'agent',
        ]);

        $agent2 = User::create([
            'name' => 'Jane Agent',
            'email' => 'jane@voip.local',
            'password' => Hash::make('password'),
            'role' => 'agent',
        ]);

        // Create agent profiles
        Agent::create([
            'user_id' => $agent1->id,
            'extension' => '1001',
            'status' => 'online',
            'sip_peer' => 'SIP/1001',
        ]);

        Agent::create([
            'user_id' => $agent2->id,
            'extension' => '1002',
            'status' => 'busy',
            'sip_peer' => 'SIP/1002',
        ]);

        // Create queues
        $queue = Queue::create([
            'name' => 'Support Queue',
            'asterisk_queue_name' => 'support',
            'max_wait_time' => 300,
            'current_calls' => 2,
            'waiting_calls' => 1,
        ]);

        // Create sample calls
        Call::create([
            'unique_id' => '1234567890.1',
            'caller_id' => '+1234567890',
            'destination' => '1001',
            'agent_id' => 1,
            'queue_id' => $queue->id,
            'status' => 'answered',
            'start_time' => now()->subMinutes(5),
            'answer_time' => now()->subMinutes(4),
        ]);

        Call::create([
            'unique_id' => '1234567890.2',
            'caller_id' => '+0987654321',
            'destination' => '1002',
            'agent_id' => 2,
            'queue_id' => $queue->id,
            'status' => 'ringing',
            'start_time' => now()->subMinutes(1),
        ]);
    }
}
