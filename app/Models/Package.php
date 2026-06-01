<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'driver',
        'price',
        'cpu',
        'ram',
        'disk',
        'frequency',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'price' => 'float'
    ];
}