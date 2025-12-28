<?php

namespace App\Providers;

use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\EventRepository;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\ReservationRepository;
use App\Services\CacheService;
use App\Services\Contracts\EventServiceInterface;
use App\Services\Contracts\ReservationServiceInterface;
use App\Services\EventService;
use App\Services\ReservationService;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CacheService::class);

        $this->app->singleton(EventRepositoryInterface::class, function ($app) {
            return new EventRepository($app->make(CacheService::class));
        });

        $this->app->singleton(ReservationRepositoryInterface::class, function ($app) {
            return new ReservationRepository($app->make(CacheService::class));
        });

        $this->app->singleton(EventServiceInterface::class, function ($app) {
            return new EventService(
                $app->make(EventRepositoryInterface::class),
                $app->make(CacheService::class)
            );
        });
        $this->app->singleton(EventService::class, EventServiceInterface::class);

        $this->app->singleton(ReservationServiceInterface::class, function ($app) {
            return new ReservationService($app->make(ReservationRepositoryInterface::class));
        });
        $this->app->singleton(ReservationService::class, ReservationServiceInterface::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
