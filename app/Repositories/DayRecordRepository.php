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
    public static function getCurrentDate() {
        $date = VariableRepository::getCurrentValueByKey('date');
        if (empty($date))
            return Carbon::today()->toDateString();
        return $date;
    }

    public static function setCurrentDate($date) {
        return VariableRepository::setCurrentValue('date', $date);
    }

    public static function createUpdateDayRecord($sleep, $meal, $weight, $height)
    {
        $date = self::getCurrentDate();

        $record = self::getDayRecord($date);
        if (empty($record)) {
            $record = new DayRecord();
            $record->day = $date;
        }
        else {
            if ($sleep == null) $sleep = $record->sleep;
            if ($meal == null) $meal = $record->meal;
            if ($weight == null) $weight = $record->weight;
            if ($height == null) $height = $record->height;
        }

        $record->weight = $weight;
        $record->meal = $meal;
        $record->sleep = $sleep;
        $record->height = $height;

        $record->save();
        return $record;
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
        $today = new Carbon(self::getCurrentDate());

        SleepRepository::wakeSleep(Carbon::today()->endOfDay()->toTimeString()); // 23:59

        // calculate sleep, meal
        $sleep_total = SleepRepository::getTodayTotalSleepAmount();
        $meal_total = MealRepository::getTodayTotalMealAmount();
        $day_record = self::createUpdateDayRecord($sleep_total, $meal_total);

        self::setCurrentDate($today->addDay());

        SleepRepository::addSleep(Carbon::today()->toTimeString()); // 00:00

        // notifications
        // check meal
        $min_meal = VariableRepository::getExpectationByKey('meal_per_day');
        if ($meal_total < $min_meal) {
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
