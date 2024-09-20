<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Policies\LibRegion\LibRegionPolicy;
use App\Models\LibRegion\LibRegion;
use App\Policies\Student\StudentPolicy;
use App\Models\Student\Student;
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
