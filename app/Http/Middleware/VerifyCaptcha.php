<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class VerifyCaptcha
{
    public function handle(Request $request, Closure $next)
    {
        $captchaProvider = Setting::where('key', 'captcha_provider')->value('value');

        if (!$captchaProvider || $captchaProvider === 'disabled') {
            return $next($request);
        }

        $token = $request->input('g-recaptcha-response') ?? $request->input('cf-turnstile-response');

        if (!$token) {
            abort(400, 'Security token missing.');
        }

        if ($captchaProvider === 'recaptcha_v2' || $captchaProvider === 'recaptcha_v3') {
            $secret = Setting::where('key', 'recaptcha_secret')->value('value');
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token
            ]);
            if (!$response->json()['success']) {
                abort(403, 'Captcha verification failed.');
            }
        }

        if ($captchaProvider === 'turnstile') {
            $secret = Setting::where('key', 'turnstile_secret')->value('value');
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token
            ]);
            if (!$response->json()['success']) {
                abort(403, 'Turnstile verification failed.');
            }
        }

        return $next($request);
    }
}