<?php

namespace App\Contracts;

interface ServerDriverInterface
{
    public function createServer(array $data): array;
    public function suspendServer($id): bool;
    public function unsuspendServer($id): bool;
    public function terminateServer($id): bool;
}