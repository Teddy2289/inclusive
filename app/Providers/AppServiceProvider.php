<?php

namespace App\Providers;

use App\Repositories\ContactRepository;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\PartenaireRepositoryInterface;
use App\Repositories\PartenaireRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PartenaireRepositoryInterface::class,
            PartenaireRepository::class
        );

         $this->app->bind(
            ContactRepositoryInterface::class,
            ContactRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
