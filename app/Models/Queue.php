<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['name', 'asterisk_queue_name', 'max_wait_time', 'current_calls', 'waiting_calls'];

    public function calls()
    {
        return $this->hasMany(Call::class);
    }
}
