<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
}
