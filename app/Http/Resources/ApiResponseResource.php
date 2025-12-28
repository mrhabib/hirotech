<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\HttpStatusCode;

class ApiResponseResource extends JsonResource
{
    public function __construct(
        public mixed $data,
        public string $message = 'Success',
        public HttpStatusCode $status = HttpStatusCode::OK,
        public ?array $meta = null
    ) {
        parent::__construct($data);
    }

    public function toResponse($request): JsonResponse
    {
        $isSuccess = $this->status->value >= 200 && $this->status->value < 300;
        
        return response()->json([
            'success' => $isSuccess,
            'message' => $this->message,
            'data' => $this->data,
            'meta' => $this->meta,
        ], $this->status->value);
    }
}
