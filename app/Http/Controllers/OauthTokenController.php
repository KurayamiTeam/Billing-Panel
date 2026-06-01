<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OauthClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OauthTokenController extends Controller
{
    public function issueToken(Request $request)
    {
        $request->validate([
            'grant_type' => 'required|string',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'code' => 'required|string',
            'redirect_uri' => 'required|string',
        ]);

        if ($request->grant_type !== 'authorization_code') {
            return response()->json(['error' => 'unsupported_grant_type'], 400);
        }

        $client = OauthClient::where('client_id', $request->client_id)
            ->where('client_secret', $request->client_secret)
            ->first();

        if (!$client || $client->redirect_uri !== $request->redirect_uri) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        $userId = Cache::pull('oauth_code_' . $request->code);

        if (!$userId) {
            return response()->json(['error' => 'invalid_grant'], 400);
        }

        $accessToken = Str::random(60);

        DB::table('oauth_access_tokens')->insert([
            'token' => hash('sha256', $accessToken),
            'user_id' => $userId,
            'oauth_client_id' => $client->id,
            'expires_at' => now()->addDays(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 2592000,
        ]);
    }
}