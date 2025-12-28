<?php

namespace App\Services\Contracts;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ReservationServiceInterface
{
    public function reserveEvent(User $user, int $eventId): Reservation;
    public function activateReservation(User $user, int $reservationId): bool;
    public function cancelReservation(User $user, int $reservationId): bool;
    public function getUserReservations(User $user): Collection;
}


