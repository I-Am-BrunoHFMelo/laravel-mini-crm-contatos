<?php

namespace App\Providers;

use App\Events\ContactScoreProcessed;
use App\Listeners\LogContactScore;
use Domain\Repositories\ContactRepositoryInterface;
use Infrastructure\Repositories\EloquentContactRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ContactRepositoryInterface::class,
            EloquentContactRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            ContactScoreProcessed::class,
            LogContactScore::class
        );
    }
}
