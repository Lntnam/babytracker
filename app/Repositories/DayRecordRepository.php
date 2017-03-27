<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 23/03/2017
 * Time: 16:21
 */

namespace App\Repositories;

use App\Models\DayRecord;
use Carbon\Carbon;

class DayRecordRepository
{
    public static function createDayRecord()
    {
        $date = Carbon::now()->subDay()->toDateString();

        $weight = WeightRepository::getCurrentWeight();
        $meal = MealRepository::getTodayTotalMealAmount();
        $sleep = SleepRepository::getTodayTotalSleepAmount();
        $poop = 0;
        $pee = 0;

        $day = new DayRecord();
        $day->weight = $weight;
        $day->meal = $meal;
        $day->sleep = $sleep;
        $day->poop = $poop;
        $day->pee = $pee;
        $day->day = $date;

        $day->save();
        return $day;
    }

    public static function getDayRecord($date)
    {
        return DayRecord::where('day', $date)->first();
    }

    public static function getPastRecords($no_of_days)
    {
        return DayRecord::orderBy('day', 'desc')->take($no_of_days)->get();
    }

    public static function closeToday() {
        SleepRepository::splitSleep();
        $day_record = self::createDayRecord();
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
        $records = self::getPastRecords($day_count);
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
    }
}
