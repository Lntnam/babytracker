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
        $current_date = DayRecordRepository::getCurrentDate();

        // Current values
        $dob = new Carbon(config('settings.baby_dob'));
        $age = CarbonInterval::days($dob->diffInDays(new Carbon($current_date)));

        $sleep = SleepRepository::getTodayTotalSleepAmount();
        $sleep = CarbonInterval::hours(floor($sleep / 60))->minute($sleep % 60);

        return view('home', [
            'notifications' => NotificationRepository::getAllUnread(),
            'weight' => WeightRepository::getCurrentWeight(),
            'height' => WeightRepository::getCurrentHeight(),
            'age' => $age,

            'meal' => MealRepository::getTodayTotalMealAmount(),
            'last_meal' => MealRepository::getLastMeal(),
            'today_meals' => MealRepository::getMealsOnDate($current_date),
            'yesterday_meals' => MealRepository::getMealsOnDate((new Carbon($current_date))->subDay()->toDateString()),

            'sleep' => $sleep,
            'sleeping_record' => SleepRepository::getCurrentSleepingRecord(),
            'last_sleep' => SleepRepository::getLatestSleep(),
            'today_sleeps' => SleepRepository::getSleepsOnDate($current_date),
            'yesterday_sleeps' => SleepRepository::getSleepsOnDate((new Carbon($current_date))->subDay()->toDateString()),

            'can_close' => Carbon::today()->gt(new Carbon($current_date)),
            'current_date' => $current_date,
        ]);
    }

    public function close() {
        DayRecordRepository::closeToday();
        return redirect()->route('dashboard');
    }
}
