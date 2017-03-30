<?php

namespace App\Http\Controllers;

use App\Repositories\DayRecordRepository;
use App\Repositories\MealRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SleepRepository;
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
        $dob = new Carbon(config('settings.baby_dob'));
        $age = CarbonInterval::days($dob->diffInDays());
        $weight = WeightRepository::getCurrentWeight();
        $height = WeightRepository::getCurrentHeight();
        $meal = MealRepository::getTodayTotalMealAmount();
        $sleep = SleepRepository::getTodayTotalSleepAmount();
        $sleep = CarbonInterval::hours(floor($sleep / 60))->minute($sleep % 60);
        $current_date = DayRecordRepository::getCurrentDate();

        // Last meal
        $last_meal = MealRepository::getLastMeal();
        $today_meals = MealRepository::getMealsOnDate($current_date);
        $yesterday_meals = MealRepository::getMealsOnDate((new Carbon($current_date))->subDay()->toDateString());

        // Sleep from
        $sleeping_record = SleepRepository::getCurrentSleepingRecord();
        $last_sleep = SleepRepository::getLatestSleep();
        $today_sleeps = SleepRepository::getSleepsOnDate($current_date);
        $yesterday_sleeps = SleepRepository::getSleepsOnDate((new Carbon($current_date))->subDay()->toDateString());

        return view('home', [
            'notifications' => $notifications,
            'weight' => $weight,
            'height' => $height,
            'age' => $age,

            'meal' => $meal,
            'last_meal' => $last_meal,
            'today_meals' => $today_meals,
            'yesterday_meals' => $yesterday_meals,

            'sleep' => $sleep,
            'sleeping_record' => $sleeping_record,
            'last_sleep' => $last_sleep,
            'today_sleeps' => $today_sleeps,
            'yesterday_sleeps' => $yesterday_sleeps,

            'can_close' => Carbon::today()->gt(new Carbon($current_date)),
            'current_date' => $current_date,
        ]);
    }

    public function close() {
        DayRecordRepository::closeToday();
        return redirect()->route('dashboard');
    }
}
