<?php

namespace App\Services\Drivers;

use App\Contracts\ServerDriverInterface;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class VirtualizorDriver implements ServerDriverInterface
{
    private $url;
    private $key;
    private $pass;

    public function __construct()
    {
        $this->url = Setting::where('key', 'virtualizor_url')->value('value');
        $this->key = Setting::where('key', 'virtualizor_api_key')->value('value');
        $this->pass = Setting::where('key', 'virtualizor_api_pass')->value('value');
    }

    public function createServer(array $data): array
    {
        return [];
    }

    public function suspendServer($id): bool
    {
        return true;
    }

    public function unsuspendServer($id): bool
    {
        return true;
    }

    public function terminateServer($id): bool
    {
        return true;
    }
}