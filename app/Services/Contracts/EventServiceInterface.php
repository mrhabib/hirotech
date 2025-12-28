<?php

namespace App\Services\Contracts;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

interface EventServiceInterface
{
    public function getAllEvents(): Collection;
    public function getEvent(int $id): Event;
    public function createEvent(array $data): Event;
    public function updateEvent(int $id, array $data): Event;
    public function deleteEvent(int $id): bool;
}


