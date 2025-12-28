<?php

namespace App\Repositories;

use App\Enums\ReservationStatus;
use App\Exceptions\CapacityExceededException;
use App\Exceptions\UserAlreadyReservedException;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\User;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Services\CacheService;
use App\Support\CacheKeys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    public function createPending(User $user, int $eventId): Reservation
    {
        return DB::transaction(function () use ($user, $eventId) {
            $event = Event::lockForUpdate()->findOrFail($eventId);
            $activeCount = $this->getActiveCountForEvent($eventId, useCache: false);
            throw_unless($activeCount < $event->capacity, CapacityExceededException::class);
            $hasActiveReservation = DB::table('reservations')
                ->where('user_id', $user->id)
                ->where('event_id', $eventId)
                ->where('status', ReservationStatus::ACTIVE->value)
                ->lockForUpdate()
                ->exists();

            throw_unless(!$hasActiveReservation, UserAlreadyReservedException::class);
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'event_id' => $eventId,
                'status' => ReservationStatus::PENDING,
            ]);
            $this->cacheService->forgetReservationCache($eventId, $user->id);

            return $reservation;
        });
    }

    public function activate(int $reservationId): bool
    {
        return DB::transaction(function () use ($reservationId) {
            $reservation = Reservation::lockForUpdate()->find($reservationId);

            if (!$reservation || $reservation->status !== ReservationStatus::PENDING) {
                return false;
            }
            $event = Event::lockForUpdate()->findOrFail($reservation->event_id);
            $activeCount = $this->getActiveCountForEvent($event->id, useCache: false);
            throw_unless($activeCount < $event->capacity, CapacityExceededException::class);
            $reservation->update(['status' => ReservationStatus::ACTIVE]);
            $this->cacheService->forgetReservationCache($event->id, $reservation->user_id);

            return true;
        });
    }

    public function cancel(int $reservationId): bool
    {
        return DB::transaction(function () use ($reservationId) {
            $reservation = Reservation::lockForUpdate()->find($reservationId);
            if (!$reservation || $reservation->status !== ReservationStatus::ACTIVE) {
                return false;
            }
            $reservation->update(['status' => ReservationStatus::CANCELLED]);
            $this->cacheService->forgetReservationCache($reservation->event_id, $reservation->user_id);

            return true;
        });
    }

    public function getUserReservations(User $user): Collection
    {
        return Cache::remember(CacheKeys::userReservations($user->id), 60, function () use ($user) {
            return $user->activeReservations()->with('event')->latest()->get();
        });
    }

    public function getActiveCountForEvent(int $eventId, bool $useCache = true): int
    {
        if (!$useCache) {
            return DB::table('reservations')
                ->where('event_id', $eventId)
                ->where('status', ReservationStatus::ACTIVE->value)
                ->count();
        }

        return Cache::remember(CacheKeys::eventActiveCount($eventId), 60, function () use ($eventId) {
            return DB::table('reservations')
                ->where('event_id', $eventId)
                ->where('status', ReservationStatus::ACTIVE->value)
                ->count();
        });
    }
}
