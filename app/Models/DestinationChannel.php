<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationChannel extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'bot_token',
        'channel_identifier',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bot_token' => 'encrypted',
    ];
}
