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
    public static function getCurrentMeal() {
        return VariableRepository::getCurrentValueByKey('meal');
    }

    public static function setCurrentMeal($value) {
        VariableRepository::setCurrentValue('meal', (float)$value);
    }

    public static function addMeal($value, $full, $at) {
        $meal = new Meal();
        $meal->at = $at;
        $date = Carbon::now();
        $time = new Carbon($at);
        if ($time->greaterThan($date)) {
            $date = $date->subDay(1);
        }

        $meal->on = $date->toDateString();
        $meal->value = $value;
        $meal->is_full = $full;

        $meal->save();
    }

    public static function getLastMealTime() {
        $meal = Meal::orderBy('on', 'desc')
            ->orderBy('at', 'desc')
            ->first();
        if ($meal) {
            $at = new Carbon($meal->at);
            if ($at->gt(new Carbon())) {
                $at->subDay();
            }
            return $at;
        }
        return null;
    }

    public static function getMeals($date) {
        return Meal::where('on', $date)->orderBy('at', 'asc')->get();
    }

    public static function getPastRecords($quantity) {
        return Meal::orderBy('on', 'desc')->take($quantity)->get();
    }
}