<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ApiResponseResource;
use Exception;

class ReservationCancellationException extends Exception
{
    public function render(): ApiResponseResource
    {
        return new ApiResponseResource(
            null,
            $this->getMessage() ?: 'Cannot cancel reservation',
            HttpStatusCode::FORBIDDEN
        );
    }
}


