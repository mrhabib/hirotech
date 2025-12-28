# Event Manager API

A Laravel-based Event Management System with Docker support. This application allows admins to manage events and users to make reservations for events.

## 🚀 Features

- **Event Management**: Create, read, update, and delete events (Admin only)
- **Reservation System**: Users can reserve events with status management (pending, active, cancelled)
- **Authentication**: Separate authentication for Admins and Users using Laravel Sanctum
- **State Machine**: Reservation status transitions with validation
- **Caching**: Redis-based caching for performance optimization
- **Repository Pattern**: Clean architecture with service and repository layers
- **DTO Pattern**: Data Transfer Objects for request validation

## 📋 Prerequisites

- Docker and Docker Compose installed
- Git

## 🐳 Docker Setup

### 1. Clone the Repository

```bash
git clone https://github.com/mrhabib/hirotech.git
cd event-manager
```

### 2. Start Docker Containers

```bash
docker-compose up -d
```

This will start the following services:
- **app**: PHP 8.4-FPM application container
- **nginx**: Web server (port 80)
- **mysql**: MySQL 8.0 database (port 3306)
- **redis**: Redis cache server (port 6379)
- **phpmyadmin**: Database management UI (port 8080)

### 3. Install Dependencies

```bash
docker-compose exec app composer install
```

### 4. Environment Configuration

Create a `.env` file in the root directory (or copy from `.env.example` if available):

```env
APP_NAME=EventManager
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

CACHE_DRIVER=redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 5. Generate Application Key

```bash
docker-compose exec app php artisan key:generate
```

### 6. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### 7. Seed Database (Optional)

```bash
docker-compose exec app php artisan db:seed
```

## 📡 API Endpoints

Base URL: `http://localhost/api`

### Authentication

#### Admin Login
```http
POST /api/admin/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "data": {
    "token": "1|...",
    "admin": { ... }
  },
  "message": "Login successful",
  "status": 200
}
```

#### User Login
```http
POST /api/user/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "data": {
    "token": "1|...",
    "user": { ... }
  },
  "message": "Login successful",
  "status": 200
}
```

### Public Endpoints

#### Get All Events
```http
GET /api/events
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Concert",
      "capacity": 100,
      "activeReservationsCount": 45
    }
  ],
  "message": "Events retrieved successfully",
  "status": 200,
  "meta": {
    "total": 1
  }
}
```

### Admin Endpoints (Requires Authentication)

All admin endpoints require the `Authorization` header:
```
Authorization: Bearer {token}
```

#### Get Admin Profile
```http
GET /api/admin/profile
Authorization: Bearer {token}
```

#### Create Event
```http
POST /api/events
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Tech Conference 2024",
  "capacity": 200
}
```

#### Get Single Event
```http
GET /api/events/{id}
Authorization: Bearer {token}
```

#### Update Event
```http
PUT /api/events/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Event Name",
  "capacity": 250
}
```

#### Delete Event
```http
DELETE /api/events/{id}
Authorization: Bearer {token}
```

### User Endpoints (Requires Authentication)

All user endpoints require the `Authorization` header:
```
Authorization: Bearer {token}
```

#### Get User Profile
```http
GET /api/user/profile
Authorization: Bearer {token}
```

#### Get User Reservations
```http
GET /api/reservations
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "eventId": 1,
      "eventName": "Concert",
      "status": "pending",
      "createdAt": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "message": "User reservations retrieved successfully",
  "status": 200,
  "meta": {
    "total": 1
  }
}
```

#### Create Reservation
```http
POST /api/reservations
Authorization: Bearer {token}
Content-Type: application/json

{
  "eventId": 1
}
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "eventId": 1,
    "eventName": "Concert",
    "status": "pending",
    "createdAt": "2024-01-01T00:00:00.000000Z"
  },
  "message": "Reservation created successfully (pending confirmation)",
  "status": 201
}
```

#### Activate Reservation
```http
PATCH /api/reservations/{reservationId}/activate
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": null,
  "message": "Reservation activated successfully",
  "status": 200
}
```

#### Cancel Reservation
```http
DELETE /api/reservations/{reservationId}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": null,
  "message": "Reservation cancelled successfully",
  "status": 204
}
```

## 🏗️ Project Structure

```
app/
├── DTOs/                          # Data Transfer Objects
│   ├── CreateEventDTO.php
│   └── UpdateEventDTO.php
│
├── Enums/                         # Enumerations
│   ├── HttpStatusCode.php
│   └── ReservationStatus.php      # pending, active, cancelled
│
├── Exceptions/                    # Custom Exceptions
│   ├── CapacityExceededException.php
│   ├── EventNotFoundException.php
│   ├── EventValidationException.php
│   ├── Handler.php
│   ├── ReservationActivationException.php
│   ├── ReservationCancellationException.php
│   └── UserAlreadyReservedException.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── EventController.php
│   │   │   └── ReservationController.php
│   │   └── Auth/
│   │       ├── AdminAuthController.php
│   │       └── UserAuthController.php
│   ├── Middleware/
│   │   └── AuthenticateApi.php   # Custom API authentication
│   ├── Requests/                  # Form Request Validation
│   │   ├── StoreEventRequest.php
│   │   ├── StoreReservationRequest.php
│   │   └── UpdateEventRequest.php
│   └── Resources/                 # API Resources
│       ├── ApiResponseResource.php
│       ├── EventResource.php
│       └── ReservationResource.php
│
├── Models/
│   ├── Admin.php
│   ├── Event.php
│   ├── Reservation.php
│   └── User.php
│
├── Providers/
│   ├── AppServiceProvider.php
│   └── RepositoryServiceProvider.php  # Repository bindings
│
├── Repositories/                  # Repository Pattern
│   ├── Contracts/
│   │   ├── EventRepositoryInterface.php
│   │   └── ReservationRepositoryInterface.php
│   ├── EventRepository.php
│   └── ReservationRepository.php
│
├── Services/                      # Business Logic Layer
│   ├── CacheService.php
│   ├── Contracts/
│   │   ├── EventServiceInterface.php
│   │   └── ReservationServiceInterface.php
│   ├── EventService.php
│   └── ReservationService.php
│
├── StateMachines/                 # State Machine Pattern
│   └── ReservationStatusMachine.php
│
└── Support/
    └── CacheKeys.php              # Cache key constants
```

## 🏛️ Architecture Patterns

### 1. **Repository Pattern**
- Abstracts database operations
- Interfaces defined in `Repositories/Contracts/`
- Implementations in `Repositories/`
- Bound in `RepositoryServiceProvider`

### 2. **Service Layer**
- Contains business logic
- Interfaces in `Services/Contracts/`
- Implementations in `Services/`
- Handles validation, state transitions, and caching

### 3. **DTO Pattern**
- Data Transfer Objects for request/response
- Located in `DTOs/`
- Ensures type safety and validation

### 4. **State Machine**
- Manages reservation status transitions
- Located in `StateMachines/ReservationStatusMachine.php`
- Validates state changes (pending → active → cancelled)

### 5. **API Resources**
- Transforms models to API responses
- Consistent response format via `ApiResponseResource`
- Located in `Http/Resources/`

## 🔐 Authentication

The application uses **Laravel Sanctum** for API authentication with two separate guards:

- **admin-api**: For admin users
- **user-api**: For regular users

Both guards use token-based authentication. Include the token in the `Authorization` header:
```
Authorization: Bearer {your-token}
```

## 💾 Database

### Tables

- **users**: Regular users who can make reservations
- **admins**: Admin users who manage events
- **events**: Events with name and capacity
- **reservations**: User reservations with status (pending/active/cancelled)

### Reservation Status Flow

1. **pending**: Initial state when reservation is created
2. **active**: Reservation is confirmed/activated
3. **cancelled**: Reservation is cancelled

## 🗄️ Cache System

The application uses **Redis** for caching to improve performance and reduce database load. The cache system is managed through the `CacheService` class with centralized cache key management via `CacheKeys`.

### Cache Strategy

#### Cached Data

1. **Event Active Reservations Count**
   - **Key**: `event.{eventId}.active_count`
   - **TTL**: 60 seconds
   - **Purpose**: Caches the count of active reservations for each event
   - **Invalidation**: Cleared when reservations are created, activated, or cancelled

2. **User Reservations**
   - **Key**: `user.{userId}.reservations`
   - **TTL**: 60 seconds
   - **Purpose**: Caches a user's active reservations list
   - **Invalidation**: Cleared when user creates, activates, or cancels a reservation

3. **Events List**
   - **Key**: `events.all`
   - **TTL**: 60 seconds
   - **Purpose**: Caches the complete list of events
   - **Invalidation**: Cleared when events are created, updated, or deleted

### Cache Invalidation

The `CacheService` provides methods for intelligent cache invalidation:

- `forgetEventCache(int $eventId)`: Clears event-specific cache
- `forgetUserReservationsCache(int $userId)`: Clears user reservations cache
- `forgetEventsList()`: Clears events list cache
- `forgetAllEventCache(int $eventId)`: Clears all event-related caches
- `forgetReservationCache(int $eventId, int $userId)`: Clears both event and user caches

### Cache Bypass in Transactions

**Important**: Cache is automatically bypassed inside database transactions to prevent reading stale data. When checking capacity or reservation status within a transaction, the system queries the database directly to ensure data consistency.

```php
// Inside transaction - cache is bypassed
$activeCount = $this->getActiveCountForEvent($eventId, useCache: false);
```

## 🔒 Lock System (Race Condition Prevention)

The application uses **database row-level locking** (`lockForUpdate()`) to prevent race conditions when multiple users attempt to reserve the same event simultaneously.

### How It Works

All critical reservation operations are wrapped in database transactions with row-level locks:

#### 1. **Creating Reservations**

```php
DB::transaction(function () {
    // Lock the event row to prevent concurrent modifications
    $event = Event::lockForUpdate()->findOrFail($eventId);
    
    // Lock reservation table rows for atomic user check
    $hasActiveReservation = DB::table('reservations')
        ->where('user_id', $user->id)
        ->where('event_id', $eventId)
        ->where('status', ReservationStatus::ACTIVE->value)
        ->lockForUpdate()
        ->exists();
    
    // Create reservation...
});
```

**What it prevents:**
- Multiple users reserving the last available spot simultaneously
- Users creating duplicate active reservations for the same event
- Capacity being exceeded due to concurrent requests

#### 2. **Activating Reservations**

```php
DB::transaction(function () {
    // Lock the reservation row
    $reservation = Reservation::lockForUpdate()->find($reservationId);
    
    // Lock the event row to check capacity atomically
    $event = Event::lockForUpdate()->findOrFail($reservation->event_id);
    
    // Check capacity and activate...
});
```

**What it prevents:**
- Multiple activation requests processing simultaneously
- Capacity being exceeded when activating pending reservations
- Race conditions during status transitions

#### 3. **Cancelling Reservations**

```php
DB::transaction(function () {
    // Lock the reservation row
    $reservation = Reservation::lockForUpdate()->find($reservationId);
    
    // Cancel reservation...
});
```

**What it prevents:**
- Concurrent cancellation attempts
- Status inconsistencies during cancellation

### Lock Mechanism Details

- **`lockForUpdate()`**: Places a SELECT ... FOR UPDATE lock on the row
- **Transaction Isolation**: All locks are held until the transaction commits or rolls back
- **Automatic Release**: Locks are automatically released when the transaction completes
- **Deadlock Prevention**: MySQL automatically handles deadlock detection and rollback

### Benefits

1. **Data Integrity**: Ensures capacity limits are never exceeded
2. **Consistency**: Prevents duplicate reservations
3. **Atomicity**: All related operations complete or fail together
4. **Performance**: Minimal overhead compared to application-level locking

### Example Scenario

**Without Locks (Race Condition):**
```
User A checks capacity: 99/100 available ✓
User B checks capacity: 99/100 available ✓
User A creates reservation: 100/100
User B creates reservation: 101/100 ❌ (Capacity exceeded!)
```

**With Locks (Safe):**
```
User A locks event → checks capacity: 99/100 → creates reservation: 100/100 ✓
User B waits for lock → locks event → checks capacity: 100/100 → throws exception ✓
```

## 🔧 Services Access

- **Application**: http://localhost
- **phpMyAdmin**: http://localhost:8080
  - Server: `mysql`
  - Username: `laravel`
  - Password: `secret`
- **MySQL**: `localhost:3306`
- **Redis**: `localhost:6379`

## 📦 Dependencies

- **Laravel 12**: PHP framework
- **Laravel Sanctum**: API authentication
- **PHP 8.4**: Programming language
- **MySQL 8.0**: Database
- **Redis**: Caching
- **Nginx**: Web server
