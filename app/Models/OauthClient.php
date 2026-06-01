<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OauthClient extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'client_id',
        'client_secret',
        'redirect_uri'
    ];

    protected static function booted()
    {
        static::creating(function ($client) {
            $client->client_id = Str::random(40);
            $client->client_secret = Str::random(80);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}