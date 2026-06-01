<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        $enabled = Setting::where('key', $provider . '_enabled')->value('value');
        if ($enabled !== '1') {
            abort(404, 'Provider disabled.');
        }

        $clientId = Setting::where('key', $provider . '_client_id')->value('value');
        $redirectUri = url("/auth/{$provider}/callback");

        $url = match ($provider) {
            'google' => "https://accounts.google.com/o/oauth2/v2/auth?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope=openid%20email%20profile",
            'github' => "https://github.com/login/oauth/authorize?client_id={$clientId}&redirect_uri={$redirectUri}&scope=user:email",
            'discord' => "https://discord.com/api/oauth2/authorize?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope=identify%20email",
            default => abort(400)
        };

        return response()->json(['redirect_url' => $url]);
    }

    public function handleProviderCallback(Request $request, $provider)
    {
        $code = $request->input('code');
        if (!$code) {
            abort(400, 'Authorization code missing.');
        }

        $clientId = Setting::where('key', $provider . '_client_id')->value('value');
        $clientSecret = Setting::where('key', $provider . '_client_secret')->value('value');
        $redirectUri = url("/auth/{$provider}/callback");

        $userData = match ($provider) {
            'github' => $this->getGithubUser($code, $clientId, $clientSecret, $redirectUri),
            'discord' => $this->getDiscordUser($code, $clientId, $clientSecret, $redirectUri),
            'google' => $this->getGoogleUser($code, $clientId, $clientSecret, $redirectUri),
            default => abort(400)
        };

        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            ['name' => $userData['name'], 'password' => bcrypt(Str::random(24))]
        );

        Auth::login($user);

        return response()->json(['status' => 'authenticated']);
    }

    private function getGithubUser($code, $clientId, $clientSecret, $redirectUri)
    {
        $tokenResponse = Http::withHeaders(['Accept' => 'application/json'])->post('https://github.com/login/oauth/access_token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri
        ]);

        $userResponse = Http::withToken($tokenResponse->json()['access_token'])->get('https://api.github.com/user');
        $emailResponse = Http::withToken($tokenResponse->json()['access_token'])->get('https://api.github.com/user/emails');

        return [
            'name' => $userResponse->json()['login'],
            'email' => $emailResponse->json()[0]['email']
        ];
    }

    private function getDiscordUser($code, $clientId, $clientSecret, $redirectUri)
    {
        $tokenResponse = Http::asForm()->post('https://discord.com/api/oauth2/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri
        ]);

        $userResponse = Http::withToken($tokenResponse->json()['access_token'])->get('https://discord.com/api/users/@me');

        return [
            'name' => $userResponse->json()['username'],
            'email' => $userResponse->json()['email']
        ];
    }

    private function getGoogleUser($code, $clientId, $clientSecret, $redirectUri)
    {
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri
        ]);

        $userResponse = Http::withToken($tokenResponse->json()['access_token'])->get('https://www.googleapis.com/oauth2/v3/userinfo');

        return [
            'name' => $userResponse->json()['name'],
            'email' => $userResponse->json()['email']
        ];
    }
}