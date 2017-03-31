<?php

namespace App\Http\Controllers;

use App\Repositories\DayRecordRepository;
use App\Repositories\MealRepository;
use Carbon\Carbon;

class MealReportController extends Controller
{
    public function index() {

        // Past 28 days
        $records = DayRecordRepository::getPastRecords(28);

        // 28 days time average
        $meals = MealRepository::getPastRecords(28);
        $meals_by_time = [];

        foreach ($meals as $meal) {
            $block = $this->getTimeBlock($meal->at);
            if (isset($meals_by_time[$block])) {
                if (isset($meals_by_time[$block][$meal->on])){
                    $meals_by_time[$block][$meal->on] += $meal->value;
                }
                else {
                    $meals_by_time[$block][$meal->on] = $meal->value;
                }
            }
            else {
                $meals_by_time[$block] = [$meal->on => $meal->value];
            }
        }
        ksort($meals_by_time);

        return view('meal', [
            'past_records' => $records,
            'meals_by_time' => $meals_by_time,
            'dob' => new Carbon(config('settings.baby_dob')),
        ]);
    }

    private function getTimeBlock($time) {
        $timeObj = new Carbon($time);
        $blocks = [
            '00:00' => '00-03',
            '03:00' => '03-06',
            '06:00' => '06-09',
            '09:00' => '09-12',
            '12:00' => '12-15',
            '15:00' => '15-18',
            '18:00' => '18-21',
            '21:00' => '21-24'
        ];

        $block = '';
        foreach ($blocks as $k => $v) {
            $blockTime = new Carbon($k);
            if ($timeObj->gte($blockTime)) {
                $block = $v;
            }
            else {
                break;
            }
        }
        return $block;
    }
}
