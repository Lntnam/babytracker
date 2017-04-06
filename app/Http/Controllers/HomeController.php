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

        $total_sleep = SleepRepository::getTodayTotalSleepAmount();
        $total_sleep = CarbonInterval::hours(floor($total_sleep / 60))->minute($total_sleep % 60);

        $yesterday_meals = MealRepository::getMealsOnDate((new Carbon($current_date))->subDay()->toDateString());
        $yesterday_total_meal = 0;
        foreach ($yesterday_meals as $meal) {
            $yesterday_total_meal += $meal->value;
        }

        $yesterday_sleeps = SleepRepository::getSleepsOnDate((new Carbon($current_date))->subDay()->toDateString());
        $yesterday_total_sleep = 0;
        foreach ($yesterday_sleeps as $sleep) {
            $yesterday_total_sleep += $sleep->hours * 60 + $sleep->minutes;
        }
        $yesterday_total_sleep = CarbonInterval::hours(floor($yesterday_total_sleep / 60))->minute($yesterday_total_sleep % 60);

        return view('home', [
            'notifications' => NotificationRepository::getAllUnread(),
            'weight' => WeightRepository::getCurrentWeight(),
            'height' => WeightRepository::getCurrentHeight(),
            'age' => $age,

            'meal' => MealRepository::getTodayTotalMealAmount(),
            'last_meal' => MealRepository::getLastMeal(),
            'today_meals' => MealRepository::getMealsOnDate($current_date),
            'yesterday_meals' => $yesterday_meals,
            'yesterday_total_meal' => $yesterday_total_meal,

            'sleep' => $total_sleep,
            'sleeping_record' => SleepRepository::getCurrentSleepingRecord(),
            'last_sleep' => SleepRepository::getLatestSleep(),
            'today_sleeps' => SleepRepository::getSleepsOnDate($current_date),
            'yesterday_sleeps' => $yesterday_sleeps,
            'yesterday_total_sleep' => $yesterday_total_sleep,

            'current_date' => $current_date,
        ]);
    }

    public function close()
    {
        DayRecordRepository::closeToday();
        return redirect()->route('dashboard');
    }
}
