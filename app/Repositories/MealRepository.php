<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 23/03/2017
 * Time: 08:21
 */

namespace App\Repositories;

use App\Models\Meal;
use Carbon\Carbon;

class MealRepository
{
    public static function getTodayTotalMealAmount()
    {
        $today = DayRecordRepository::getCurrentDate();
        $meals = self::getMealsOnDate($today);
        $total = 0;
        foreach ($meals as $meal) {
            $total += $meal->value;
        }
        return $total;
    }

    public static function addUpdateMeal($value, $at, $type)
    {
        $today = DayRecordRepository::getCurrentDate();

        // find if meal exists
        $meal = Meal::where([
            ['on', $today],
            ['at', $at]
        ])->first();

        if (empty($meal)) {
            $meal = new Meal();
            $meal->on = $today;
            $meal->at = $at;
        }

        $meal->value = $value;
        $meal->feed_type = $type;

        $meal->save();
        return $meal;
    }

    public static function getLastMeal()
    {
        return Meal::orderBy('on', 'desc')
            ->orderBy('at', 'desc')
            ->first();
    }

    public static function getMealsOnDate($date)
    {
        return Meal::where('on', $date)
            ->orderBy('at', 'asc')
            ->get();
    }

    public static function getPastRecords($no_of_days = 0)
    {
        if ($no_of_days > 0) {
            $today = new Carbon(DayRecordRepository::getCurrentDate());
            $date = $today->copy()->subDay($no_of_days - 1);
            return Meal::whereBetween('on', [$date->toDateString(), $today->toDateString()])
                ->orderBy('on', 'asc')
                ->orderBy('at', 'asc')
                ->get();
        }
        else {
            return Meal::orderBy('on', 'asc')
                ->orderBy('at', 'asc')
                ->get();
        }
    }
}
