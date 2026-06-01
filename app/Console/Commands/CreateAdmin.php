<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class CreateAdmin extends Command
{
    protected $signature = 'kurayami:install';
    protected $description = '';

    public function handle()
    {
        $this->info('--- Kurayami Panel Installation ---');

        $url = $this->ask('Site URL (e.g., https://panel.domain.com)');
        $this->changeEnv(['APP_URL' => $url]);

        $email = $this->ask('Admin Email');
        $password = $this->secret('Admin Password');

        Artisan::call('migrate', ['--force' => true]);

        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info('Installation completed successfully.');
    }

    private function changeEnv(array $data)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            foreach ($data as $key => $value) {
                file_put_contents($path, preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    file_get_contents($path)
                ));
            }
        }
    }
}