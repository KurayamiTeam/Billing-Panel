<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OauthTokenController;
use App\Http\Controllers\OauthUserController;

Route::post('/oauth/token', [OauthTokenController::class, 'issueToken']);
Route::middleware('api')->get('/oauth/user', [OauthUserController::class, 'userInformation']);