<?php

namespace App\Support;

class CacheKeys
{
    public static function eventActiveCount(int $eventId): string
    {
        return "event.{$eventId}.active_count";
    }

    public static function userReservations(int $userId): string
    {
        return "user.{$userId}.reservations";
    }

    public static function eventsList(): string
    {
        return 'events.all';
    }
}


