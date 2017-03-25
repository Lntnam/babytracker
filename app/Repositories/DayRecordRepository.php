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
        $meal = MealRepository::getCurrentMeal();
        $sleep = SleepRepository::getCurrentSleep();
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

    public static function getLatestDayRecords($quantity)
    {
        return DayRecord::orderBy('day', 'desc')
            ->take($quantity)
            ->get();
    }

    public static function getPastRecords($quantity)
    {
        return DayRecord::orderBy('day', 'desc')->take($quantity)->get();
    }
}
