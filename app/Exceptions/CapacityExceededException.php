<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ApiResponseResource;

class CapacityExceededException extends \Exception
{
    public function render()
    {
        return new ApiResponseResource(
            null,
            'Event capacity exceeded',
            HttpStatusCode::FORBIDDEN
        );
    }
}
