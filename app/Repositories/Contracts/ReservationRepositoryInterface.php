<?php

namespace App\Repositories\Contracts;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface
{
    public function createPending(User $user, int $eventId): Reservation;
    public function activate(int $reservationId): bool;
    public function cancel(int $reservationId): bool;
    public function getUserReservations(User $user): Collection;
    public function getActiveCountForEvent(int $eventId, bool $useCache = true): int;
}
