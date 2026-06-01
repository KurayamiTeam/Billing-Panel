<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Services\ServerManager;
use Carbon\Carbon;

class SuspendExpiredServers extends Command
{
    protected $signature = 'kurayami:cron';
    protected $description = '';

    public function handle()
    {
        $servers = Server::where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->get();

        foreach ($servers as $server) {
            try {
                $driver = ServerManager::make($server->driver);
                if ($driver->suspendServer($server->external_id)) {
                    $server->update(['status' => 'suspended']);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
