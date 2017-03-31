<?php

namespace App\Http\Controllers;

use App\Repositories\DayRecordRepository;
use App\Repositories\MealRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SleepRepository;
use App\Repositories\WeightRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function closeNotification(Request $request)
    {
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

    public function addMeal(Request $request)
    {
        $value = $request->input('value');
        $type = $request->input('type');
        $at = $request->input('at');

        MealRepository::addUpdateMeal($value, $at, $type);

        return MealRepository::getTodayTotalMealAmount();
    }

    public function toggleSleep(Request $request)
    {
        $sleeping = SleepRepository::isSleeping();

        if (!$sleeping) { // go to sleep
            SleepRepository::addSleep($request->input('sleep_time'));
        } else { // wake up
            SleepRepository::wakeSleep($request->input('wake_time'));
        }

        $sleeping = !$sleeping;
        return response()->json(['sleeping'=>$sleeping]);
    }

    public function cancelSleep()
    {
        SleepRepository::deleteCurrentSleeping();
    }

    public function loadSleepStatusView()
    {
        return view('sub.sleep_status', [
            'sleeping_record' => SleepRepository::getCurrentSleepingRecord(),
        ]);
    }

    public function loadAgeWeightHeightView()
    {
        $today = DayRecordRepository::getCurrentDate();
        $dob = new Carbon(config('settings.baby_dob'));
        $age = CarbonInterval::days($dob->diffInDays(new Carbon($today)));

        return view('sub.age_weight_height', [
            'weight' => WeightRepository::getCurrentWeight(),
            'height' => WeightRepository::getCurrentHeight(),
            'age' => $age,
        ]);
    }

    public function loadTodayMealsView() {
        return view('sub.meals_table', [
            'meal_list' => MealRepository::getMealsOnDate(DayRecordRepository::getCurrentDate())
        ]);
    }

    public function loadMealSnapshotView() {
        return view('sub.meal_snapshot', [
            'last_meal' => MealRepository::getLastMeal(),
            'meal' => MealRepository::getTodayTotalMealAmount()
        ]);
    }

    public function loadTodaySleepsView() {
        return view('sub.sleeps_table', [
            'sleep_list' => SleepRepository::getSleepsOnDate(DayRecordRepository::getCurrentDate())
        ]);
    }

    public function loadSleepSnapshotView() {
        $sleep = SleepRepository::getTodayTotalSleepAmount();
        $sleep = CarbonInterval::hours(floor($sleep / 60))->minute($sleep % 60);

        return view('sub.sleep_snapshot', [
            'last_sleep' => SleepRepository::getLatestSleep(),
            'sleep' => $sleep
        ]);
    }
}
