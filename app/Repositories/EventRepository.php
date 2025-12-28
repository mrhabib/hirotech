<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Services\CacheService;
use App\Support\CacheKeys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class EventRepository implements EventRepositoryInterface
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    public function all(): Collection
    {
        return Cache::remember(CacheKeys::eventsList(), 60, fn() => Event::orderByDesc('id')->get());
    }

    public function find(int $id): ?Event
    {
        return Event::find($id);
    }

    public function create(array $data): Event
    {
        $this->cacheService->forgetEventsList();
        return Event::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $event = $this->find($id);
        if (!$event) return false;

        $this->cacheService->forgetEventsList();
        return $event->update($data);
    }

    public function delete(int $id): bool
    {
        $event = $this->find($id);
        if (!$event) return false;

        $this->cacheService->forgetEventsList();
        return $event->delete();
    }
}
