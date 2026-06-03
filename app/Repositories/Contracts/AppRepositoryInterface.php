<?php

namespace App\Repositories\Contracts;

use App\Models\App;
use Illuminate\Database\Eloquent\Collection;

interface AppRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?App;

    public function create(array $data): App;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getUsersApps(int $userId): Collection;
}
