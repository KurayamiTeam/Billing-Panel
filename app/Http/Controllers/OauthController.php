<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OauthClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class OauthController extends Controller
{
    public function authorize(Request $request)
    {
        $client = OauthClient::where('client_id', $request->client_id)->first();

        if (!$client || $client->redirect_uri !== $request->redirect_uri) {
            abort(400, 'Invalid client configuration.');
        }

        return response()->json([
            'client_name' => $client->name,
            'state' => $request->state
        ]);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'redirect_uri' => 'required',
            'approve' => 'required|boolean'
        ]);

        $client = OauthClient::where('client_id', $request->client_id)->first();

        if (!$client || $client->redirect_uri !== $request->redirect_uri) {
            abort(400);
        }

        if (!$request->approve) {
            return redirect($client->redirect_uri . '?error=access_denied');
        }

        $code = Str::random(40);
        Cache::put('oauth_code_' . $code, Auth::id(), 600);

        return redirect($client->redirect_uri . '?code=' . $code . '&state=' . $request->state);
    }
}