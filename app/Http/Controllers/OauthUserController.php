<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class OauthUserController extends Controller
{
    public function userInformation(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $hashedToken = hash('sha256', $token);
        $accessToken = DB::table('oauth_access_tokens')
            ->where('token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$accessToken) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $user = User::find($accessToken->user_id);

        if (!$user) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
}
