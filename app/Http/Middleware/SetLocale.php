<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Setting;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Setting::where('key', 'default_locale')->value('value') ?? 'es';

        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        if (in_array($locale, ['es', 'en', 'pt', 'fr'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}