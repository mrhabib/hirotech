<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ApiResponseResource;
use Exception;

class ReservationActivationException extends Exception
{
    public function render(): ApiResponseResource
    {
        return new ApiResponseResource(
            null,
            $this->getMessage() ?: 'Cannot activate reservation',
            HttpStatusCode::FORBIDDEN
        );
    }
}


