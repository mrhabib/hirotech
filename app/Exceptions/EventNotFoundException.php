<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ApiResponseResource;
use Exception;

class EventNotFoundException extends Exception
{
    public function render(): ApiResponseResource
    {
        return new ApiResponseResource(
            null,
            'Event not found',
            HttpStatusCode::NOT_FOUND
        );
    }
}
