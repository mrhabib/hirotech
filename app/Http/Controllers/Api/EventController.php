<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\EventResource;
use App\Services\Contracts\EventServiceInterface;

class EventController extends Controller
{
    public function __construct(private EventServiceInterface $eventService) {}

    public function index()
    {
        $events = $this->eventService->getAllEvents();
        return new ApiResponseResource(
            EventResource::collection($events),
            'Events retrieved successfully',
            HttpStatusCode::OK,
            ['total' => $events->count()]
        );
    }
    public function store(StoreEventRequest $request)
    {
        $event = $this->eventService->createEvent($request->validated());
        return new ApiResponseResource(
            new EventResource($event),
            'Event created successfully',
            HttpStatusCode::CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $event = $this->eventService->getEvent($id);
        return new ApiResponseResource(
            new EventResource($event),
            'Event retrieved successfully',
            HttpStatusCode::OK
        );
    }

    public function update(UpdateEventRequest $request, int $id)
    {
        $event = $this->eventService->updateEvent($id, $request->validated());
        return new ApiResponseResource(
            new EventResource($event),
            'Event updated successfully',
            HttpStatusCode::OK
        );
    }

    public function destroy(int $id)
    {
        $this->eventService->deleteEvent($id);
        return new ApiResponseResource(
            null,
            'Event deleted successfully',
            HttpStatusCode::NO_CONTENT
        );
    }
}
