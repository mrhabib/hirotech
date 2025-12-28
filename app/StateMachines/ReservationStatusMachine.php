<?php

namespace App\StateMachines;

use App\Enums\ReservationStatus;

class ReservationStatusMachine
{
    public function canTransition(ReservationStatus $from, ReservationStatus $to): bool
    {
        return match ($from) {
            ReservationStatus::PENDING => in_array($to, [
                ReservationStatus::ACTIVE,
                ReservationStatus::CANCELLED,
            ]),
            ReservationStatus::ACTIVE => $to === ReservationStatus::CANCELLED,
            ReservationStatus::CANCELLED => false, // Cannot transition from cancelled
        };
    }
    public function getAllowedTransitions(ReservationStatus $from): array
    {
        return match ($from) {
            ReservationStatus::PENDING => [
                ReservationStatus::ACTIVE,
                ReservationStatus::CANCELLED,
            ],
            ReservationStatus::ACTIVE => [
                ReservationStatus::CANCELLED,
            ],
            ReservationStatus::CANCELLED => [],
        };
    }
    public function transition(ReservationStatus $from, ReservationStatus $to): bool
    {
        if (!$this->canTransition($from, $to)) {
            return false;
        }

        return true;
    }
}


