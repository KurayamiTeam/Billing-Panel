<?php

namespace App\Services\Drivers;

use App\Contracts\ServerDriverInterface;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class PterodactylDriver implements ServerDriverInterface
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

    public function createServer(array $data): array
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

    public function suspendServer($id): bool
    {
        return $this->client()->post($this->url . "/api/application/servers/{$id}/suspend")->successful();
    }

    public function unsuspendServer($id): bool
    {
        return $this->client()->post($this->url . "/api/application/servers/{$id}/unsuspend")->successful();
    }

    public function terminateServer($id): bool
    {
        return $this->client()->delete($this->url . "/api/application/servers/{$id}")->successful();
    }
}