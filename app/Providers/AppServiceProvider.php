<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $name = config('settings.baby_name');
        $dob = new Carbon(config('settings.baby_dob'));
        $age = CarbonInterval::days($dob->diffInDays());

        View::share('name', $name);
        View::share('dob', $dob);
        View::share('age', $age);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
