<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Policies\LibRegion\LibRegionPolicy;
use App\Models\LibRegion\LibRegion;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
