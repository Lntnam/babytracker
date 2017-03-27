<?php

namespace App\Http\Controllers;

use App\Repositories\DayRecordRepository;
use App\Repositories\MealRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SleepRepository;
use App\Repositories\VariableRepository;
use App\Repositories\WeightRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class HomeController extends Controller
{
    public function index()
    {
        // Get notifications
        $notifications = NotificationRepository::getAllUnread();

        // Current values
        $weight = WeightRepository::getCurrentWeight();
        $meal = MealRepository::getTodayTotalMealAmount();
        $sleep = SleepRepository::getTodayTotalSleepAmount();
        $sleep = CarbonInterval::hours(floor($sleep / 60))->minute($sleep % 60);
        $sleeping = SleepRepository::isSleeping();
        $name = config('settings.baby_name');

        $dob = new Carbon(config('settings.baby_dob'));
        $age = CarbonInterval::days($dob->diffInDays());

        // Weight trend
        $yesterdayWeight = WeightRepository::getYesterdayWeight();

        // Last meal
        /** @var Carbon $lastMeal */
        $lastMeal = MealRepository::getLastMealTime();

        // Sleep from
        $sleeping_record = SleepRepository::getCurrentSleepingRecord();

        // Last sleep
        $last_sleep = SleepRepository::getLatestSleep();

        return view('home', [
            'notifications' => $notifications,
            'name' => $name,
            'age' => $age,
            'weight' => $weight,
            'meal' => $meal,
            'sleep' => $sleep,
            'sleeping' => $sleeping,
            'sleeping_record' => $sleeping_record,
            'last_weight' => $yesterdayWeight,
            'last_meal' => $lastMeal,
            'last_sleep' => $last_sleep
        ]);
    }

    public function close() {
        DayRecordRepository::closeToday();
        return redirect()->route('dashboard');
    }
}
