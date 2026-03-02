<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'unique_id', 'caller_id', 'destination', 'agent_id', 'queue_id',
        'status', 'start_time', 'answer_time', 'end_time', 'duration', 'wait_time'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'answer_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }
}
