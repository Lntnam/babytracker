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
        // $last_sleep = SleepRepository::

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
            'last_meal' => $lastMeal
        ]);
    }

    public function close() {
        SleepRepository::splitSleep();
        $day_record = DayRecordRepository::createDayRecord();
        VariableRepository::clearCurrentValues();

        // notifications
        // check meal
        $min_meal = VariableRepository::getExpectationByKey('meal_per_day');
        if ($day_record->meal < $min_meal) {
            NotificationRepository::createNotification('warning', 'Beware!', 'Less than '.$min_meal.'ml on '.$day_record->day.'.');
        }
        // check weight
        $min_weight_inc = VariableRepository::getExpectationByKey('gram_per_day');
        $day_count = ceil(100 / $min_weight_inc);
        $records = DayRecordRepository::getPastRecords($day_count);
        $min_weight = 0;
        $max_weight = 0;
        foreach ($records as $r) {
            $min_weight = min($min_weight, $r->weight);
            $max_weight = max($max_weight, $r->weight);
        }
        if ($min_weight > $max_weight) {
            NotificationRepository::createNotification('danger', 'Alert!', 'Weight drop during the last '.$day_count.' days.');
        }
        else if ($min_weight == $max_weight) {
            NotificationRepository::createNotification('warning', 'Alert!', 'Weight not increasing during the last '.$day_count.' days.');
        }

        return redirect()->route('dashboard');
    }
}
