<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ApiResponseResource;

class UserAlreadyReservedException extends \Exception
{
    public function render()
    {
        return new ApiResponseResource(
            null,
            'User already has active reservation for this event',
            HttpStatusCode::FORBIDDEN
        );
    }
}
