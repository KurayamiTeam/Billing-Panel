<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class PterodactylService
{
    private $url;
    private $key;

    public function __construct()
    {
        $this->url = Setting::where('key', 'pterodactyl_url')->value('value');
        $this->key = Setting::where('key', 'pterodactyl_api_key')->value('value');
    }

    private function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    public function createUser(array $data)
    {
        $response = $this->client()->post($this->url . '/api/application/users', [
            'username' => $data['username'],
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);

        return $response->json();
    }

    public function createServer(array $data)
    {
        $response = $this->client()->post($this->url . '/api/application/servers', [
            'name' => $data['name'],
            'user' => $data['pterodactyl_user_id'],
            'egg' => $data['egg_id'],
            'docker_image' => $data['image'],
            'startup' => $data['startup'],
            'limits' => [
                'memory' => $data['ram'],
                'swap' => 0,
                'disk' => $data['disk'],
                'io' => 500,
                'cpu' => $data['cpu'],
            ],
            'feature_limits' => [
                'databases' => $data['databases'] ?? 0,
                'allocations' => $data['allocations'] ?? 1,
            ],
            'allocation' => [
                'default' => $data['allocation_id'],
            ],
        ]);

        return $response->json();
    }

    public function suspendServer($id)
    {
        return $this->client()->post($this->url . "/api/application/servers/{$id}/suspend")->successful();
    }

    public function unsuspendServer($id)
    {
        return $this->client()->post($this->url . "/api/application/servers/{$id}/unsuspend")->successful();
    }
}