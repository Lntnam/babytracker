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
        $weight = WeightRepository::getCurrentWeight();
        $meal = MealRepository::getTodayTotalMealAmount();
        $sleep = SleepRepository::getTodayTotalSleepAmount();
        $sleep = CarbonInterval::hours(floor($sleep / 60))->minute($sleep % 60);

        // Last meal
        $last_meal = MealRepository::getLastMeal();
        $today_meals = MealRepository::getMealsOnDate(Carbon::today()->toDateString());
        $yesterday_meals =MealRepository::getMealsOnDate(Carbon::today()->subDay()->toDateString());

        // Sleep from
        $sleeping_record = SleepRepository::getCurrentSleepingRecord();
        $last_sleep = SleepRepository::getLatestSleep();
        $today_sleeps = SleepRepository::getSleepsOnDate(Carbon::today()->toDateString());
        $yesterday_sleeps = SleepRepository::getSleepsOnDate(Carbon::today()->subDay()->toDateString());

        return view('home', [
            'notifications' => $notifications,
            'weight' => $weight,

            'meal' => $meal,
            'last_meal' => $last_meal,
            'today_meals' => $today_meals,
            'yesterday_meals' => $yesterday_meals,

            'sleep' => $sleep,
            'sleeping_record' => $sleeping_record,
            'last_sleep' => $last_sleep,
            'today_sleeps' => $today_sleeps,
            'yesterday_sleeps' => $yesterday_sleeps
        ]);
    }

    public function close() {
        DayRecordRepository::closeToday();
        return redirect()->route('dashboard');
    }
}
