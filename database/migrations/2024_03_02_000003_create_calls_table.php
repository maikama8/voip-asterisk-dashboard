<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('caller_id');
            $table->string('destination');
            $table->foreignId('agent_id')->nullable()->constrained();
            $table->foreignId('queue_id')->nullable()->constrained();
            $table->enum('status', ['ringing', 'answered', 'completed', 'abandoned'])->default('ringing');
            $table->timestamp('start_time');
            $table->timestamp('answer_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('wait_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
