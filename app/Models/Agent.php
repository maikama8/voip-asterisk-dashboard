<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agent extends Model
{
    protected $fillable = ['user_id', 'extension', 'status', 'sip_peer', 'last_activity'];

    protected $casts = ['last_activity' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }
}
