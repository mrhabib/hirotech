<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Services\Contracts\EventServiceInterface;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\EventNotFoundException;

class EventService implements EventServiceInterface
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private CacheService $cacheService
    ) {}
    public function getAllEvents(): Collection
    {
        return $this->eventRepository->all();
    }
    public function getEvent(int $id): Event
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            throw new EventNotFoundException();
        }
        return $event;
    }
    public function createEvent(array $data): Event
    {
        $event = $this->eventRepository->create($data);
        $this->cacheService->forgetEventsList();
        return $event;
    }
    public function updateEvent(int $id, array $data): Event
    {
        $event = $this->getEvent($id);
        $this->eventRepository->update($id, $data);
        $this->cacheService->forgetEventsList();
        // Return fresh event data
        return $this->getEvent($id);
    }

    public function deleteEvent(int $id): bool
    {
        $this->getEvent($id); // Throws if not found
        $this->cacheService->forgetEventsList();
        return $this->eventRepository->delete($id);
    }
}
