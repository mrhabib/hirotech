<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use App\Support\CacheKeys;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'capacity'];
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function activeReservationsCount(): int
    {
        return Cache::remember(CacheKeys::eventActiveCount($this->id), 60, function () {
            return $this->reservations()->where('status', ReservationStatus::ACTIVE->value)->count();
        });
    }

    public function hasCapacity(): bool
    {
        return $this->activeReservationsCount() < $this->capacity;
    }
}
