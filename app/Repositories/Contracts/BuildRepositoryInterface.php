<?php

namespace App\Repositories\Contracts;

use App\Models\Build;
use Illuminate\Database\Eloquent\Collection;

interface BuildRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Build;

    public function create(array $data): Build;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getAppBuilds(int $appId): Collection;
}
