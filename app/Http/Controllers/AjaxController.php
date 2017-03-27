<?php

namespace App\Http\Controllers;

use App\Repositories\MealRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SleepRepository;
use App\Repositories\WeightRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function closeNotification(Request $request) {
        $id = $request->input('id');
        $notification = NotificationRepository::closeNotification($id);

        return response()->json(!empty($notification) ? 1 : 0);
    }

    public function saveWeight(Request $request)
    {
        $value = $request->input('value');

        WeightRepository::setCurrentWeight($value);
        return $value;
    }

    public function addMeal(Request $request) {
        $value = $request->input('value');
        $full = $request->input('full');
        $at = $request->input('at');

        MealRepository::addUpdateMeal($value, $full, $at);

        $today_meals = MealRepository::getMealsOnDate(Carbon::today()->toDateString());
        $today_amount = 0;
        foreach ($today_meals as $meal) {
            $today_amount += $meal->value;
        }
        MealRepository::setTodayTotalMeanAmount($today_amount);

        return $today_amount;
    }

    public function toggleSleep(Request $request) {
        $sleeping = SleepRepository::getCurrentSleepingRecord();
        if (empty($sleeping)) { // go to sleep
            $time = new Carbon($request->input('sleep_time'));
            if ($time->gt(Carbon::now())) $time->subday();

            SleepRepository::addSleep($time->toDateString(), $time->toTimeString());

            return 0;
        }
        else { // wake up
            $time = new Carbon($request->input('wake_time'));
            if ($time->gt(Carbon::now())) $time->subday();

            $sleep_value = SleepRepository::wakeSleep($time->toTimeString());
            $interval = CarbonInterval::hours(floor($sleep_value / 60))->minute($sleep_value % 60);
            return $interval->hours . 'h ' . $interval->minutes . 'm';
        }
    }

    public function cancelSleep() {
        SleepRepository::deleteCurrentSleeping();
    }
}
