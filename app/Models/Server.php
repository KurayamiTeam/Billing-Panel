<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'user_id',
        'driver',
        'external_id',
        'uuid',
        'name',
        'status',
        'cpu',
        'ram',
        'disk',
        'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}