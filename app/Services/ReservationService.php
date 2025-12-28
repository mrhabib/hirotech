<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\User;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Services\Contracts\ReservationServiceInterface;
use App\Exceptions\ReservationActivationException;
use App\Exceptions\ReservationCancellationException;
use Illuminate\Database\Eloquent\Collection;

class ReservationService implements ReservationServiceInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {}
    public function reserveEvent(User $user, int $eventId): Reservation
    {
        return $this->reservationRepository->createPending($user, $eventId);
    }
    public function activateReservation(User $user, int $reservationId): bool
    {
        $reservation = $user->reservations()->where('id', $reservationId)->first();
        if (!$reservation) {
            throw new ReservationActivationException('Reservation not found or does not belong to user');
        }
        $success = $this->reservationRepository->activate($reservationId);
        if (!$success) {
            throw new ReservationActivationException('Cannot activate reservation. It may not be in pending status or capacity exceeded.');
        }

        return true;
    }
    public function cancelReservation(User $user, int $reservationId): bool
    {
        $reservation = $user->reservations()->where('id', $reservationId)->first();
        if (!$reservation) {
            throw new ReservationCancellationException('Reservation not found or does not belong to user');
        }

        $success = $this->reservationRepository->cancel($reservationId);
        if (!$success) {
            throw new ReservationCancellationException('Cannot cancel reservation. It may not be in active status.');
        }

        return true;
    }
    public function getUserReservations(User $user): Collection
    {
        return $this->reservationRepository->getUserReservations($user);
    }
}
