<?php

namespace App\Repositories\Eloquent;

use App\Models\App;
use App\Repositories\Contracts\AppRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AppRepository implements AppRepositoryInterface
{
    public function all(): Collection
    {
        return App::all();
    }

    public function find(int $id): ?App
    {
        return App::find($id);
    }

    public function create(array $data): App
    {
        return App::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $app = $this->find($id);
        if ($app) {
            return $app->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $app = $this->find($id);
        if ($app) {
            return $app->delete();
        }
        return false;
    }

    public function getUsersApps(int $userId): Collection
    {
        return App::where('user_id', $userId)->get();
    }
}
