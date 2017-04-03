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

    public static function createUpdateDayRecord($sleep, $meal, $weight = null, $height = null)
    {
        $date = self::getCurrentDate();

        $record = self::getDayRecord($date);
        if (empty($record)) {
            $record = new DayRecord();
            $record->day = $date;
        }
        else {
            if ($sleep === null) $sleep = $record->sleep;
            if ($meal === null) $meal = $record->meal;
            if ($weight === null) $weight = $record->weight;
            if ($height === null) $height = $record->height;
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
        return DayRecord::whereNotNull('meal')
            ->orderBy('day', 'asc')
            ->take($no_of_days)
            ->get();
    }

    public static function closeToday() {
        $today = new Carbon(self::getCurrentDate());

        $sleeping = SleepRepository::wakeSleep(Carbon::today()->endOfDay()->toTimeString()); // 23:59

        // calculate sleep, meal
        $sleep_total = SleepRepository::getTodayTotalSleepAmount();
        $meal_total = MealRepository::getTodayTotalMealAmount();
        self::createUpdateDayRecord($sleep_total, $meal_total);

        self::setCurrentDate($today->addDay()->toDateString());

        if ($sleeping)
            SleepRepository::addSleep(Carbon::today()->toTimeString()); // 00:00
    }
}
