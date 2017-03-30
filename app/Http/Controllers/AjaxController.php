<?php

namespace App\Http\Controllers;

use App\Repositories\MealRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SleepRepository;
use App\Repositories\WeightRepository;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function closeNotification(Request $request) {
        $id = $request->input('id');
        $notification = NotificationRepository::closeNotification($id);

        return response()->json(!empty($notification) ? 1 : 0);
    }

    public function saveMeasurements(Request $request)
    {
        $weight = $request->input('weight');
        $height = $request->input('height');

        WeightRepository::updateWeightHeight($weight, $height);
        WeightRepository::setCurrentWeight($weight);
        WeightRepository::setCurrentHeight($height);
    }

    public function addMeal(Request $request) {
        $value = $request->input('value');
        $type = $request->input('type');
        $at = $request->input('at');

        MealRepository::addUpdateMeal($value, $at, $type);
    }

    public function toggleSleep(Request $request) {
        $sleeping = SleepRepository::getCurrentSleepingRecord();
        if (empty($sleeping)) { // go to sleep
            SleepRepository::addSleep($request->input('sleep_time'));
        }
        else { // wake up
            SleepRepository::wakeSleep($request->input('wake_time'));
        }
    }

    public function cancelSleep() {
        SleepRepository::deleteCurrentSleeping();
    }
}
