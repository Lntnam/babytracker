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
        return VariableRepository::getCurrentValueByKey('meal');
    }

    public static function setTodayTotalMeanAmount($value)
    {
        VariableRepository::setCurrentValue('meal', (float)$value);
    }

    public static function addUpdateMeal($value, $at, $type)
    {
        $datetime = new Carbon($at);
        if ($datetime->greaterThan(Carbon::now())) {
            $datetime->subDay();
        }

        // find if meal exists
        $meal = Meal::where([
            ['on', $datetime->toDateString()],
            ['at', $datetime->toTimeString()]
        ])->first();

        if (empty($meal)) {
            $meal = new Meal();
            $meal->on = $datetime->toDateString();
        }

        $meal->at = $at;
        $meal->value = $value;
        $meal->feed_type = $type;

        $meal->save();
    }

    public static function getLastMeal()
    {
        return Meal::orderBy('on', 'desc')
            ->orderBy('at', 'desc')
            ->first();
    }

    public static function getMealsOnDate($date)
    {
        return Meal::where('on', $date)->orderBy('at', 'asc')->get();
    }

    public static function getPastRecords($no_of_days)
    {
        $date = Carbon::today()->subDay($no_of_days - 1);
        return Meal::where('on', '>=', $date->toDateString())
            ->orderBy('on', 'asc')
            ->orderBy('at', 'asc')
            ->get();
    }
}
