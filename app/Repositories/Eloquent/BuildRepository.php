<?php

namespace App\Repositories\Eloquent;

use App\Models\Build;
use App\Repositories\Contracts\BuildRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BuildRepository implements BuildRepositoryInterface
{
    public function all(): Collection
    {
        return Build::all();
    }

    public function find(int $id): ?Build
    {
        return Build::find($id);
    }

    public function create(array $data): Build
    {
        return Build::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $build = $this->find($id);
        if ($build) {
            return $build->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $build = $this->find($id);
        if ($build) {
            return $build->delete();
        }
        return false;
    }

    public function getAppBuilds(int $appId): Collection
    {
        return Build::where('app_id', $appId)->orderBy('created_at', 'desc')->get();
    }
}
