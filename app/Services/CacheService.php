<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function forgetEventCache(int $eventId): void
    {
        Cache::forget("event.{$eventId}.active_count");
    }
    public function forgetUserReservationsCache(int $userId): void
    {
        Cache::forget("user.{$userId}.reservations");
    }
    public function forgetEventsList(): void
    {
        Cache::forget('events.all');
    }
    public function forgetAllEventCache(int $eventId): void
    {
        $this->forgetEventCache($eventId);
        $this->forgetEventsList();
    }
    public function forgetReservationCache(int $eventId, int $userId): void
    {
        $this->forgetEventCache($eventId);
        $this->forgetUserReservationsCache($userId);
    }
}


