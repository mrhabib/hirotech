<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\ReservationResource;
use App\Services\Contracts\ReservationServiceInterface;

class ReservationController extends Controller
{
    public function __construct(private ReservationServiceInterface $reservationService) {}

    public function index()
    {
        $user = auth('user-api')->user();
        $reservations = $this->reservationService->getUserReservations($user);

        return new ApiResponseResource(
            ReservationResource::collection($reservations),
            'User reservations retrieved successfully',
            HttpStatusCode::OK,
            ['total' => $reservations->count()]
        );
    }

    public function store(StoreReservationRequest $request)
    {
        $user = auth('user-api')->user();
        // eventId is validated in StoreReservationRequest
        $validatedEventId = (int) $request->validated()['eventId'];
        $reservation = $this->reservationService->reserveEvent($user, $validatedEventId);

        return new ApiResponseResource(
            new ReservationResource($reservation),
            'Reservation created successfully (pending confirmation)',
            HttpStatusCode::CREATED
        );
    }

    public function activate(int $reservationId)
    {
        $user = auth('user-api')->user();
        $this->reservationService->activateReservation($user, $reservationId);

        return new ApiResponseResource(
            null,
            'Reservation activated successfully',
            HttpStatusCode::OK
        );
    }

    public function destroy(int $reservationId)
    {
        $user = auth('user-api')->user();
        $this->reservationService->cancelReservation($user, $reservationId);

        return new ApiResponseResource(
            null,
            'Reservation cancelled successfully',
            HttpStatusCode::NO_CONTENT
        );
    }
}

