<?php

use Illuminate\Support\Facades\Route;
use App\Models\Package;

Route::get('/', function () {
    $packages = Package::all();
    return view('index', compact('packages'));
});