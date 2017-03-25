<?php

namespace App\Http\Controllers;

use App\Repositories\DayRecordRepository;
use App\Repositories\SleepRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SleepReportController extends Controller
{
    public function index() {
        // Past 10 days
        $records = DayRecordRepository::getPastRecords(10);

        // 10 days time average
        $sleeps = SleepRepository::getPastRecords(10);
        $sleeps_by_time = [];

        foreach ($sleeps as $sleep) {
            $block = $this->getTimeBlock($sleep->sleep);
            if (isset($sleeps_by_time[$block])) {
                $sleeps_by_time[$block] += $sleep->hours * 60 + $sleep->minutes;
            }
            else {
                $sleeps_by_time[$block] = $sleep->hours * 60 + $sleep->minutes;
            }
        }
        ksort($sleeps_by_time);

        return view('sleep', [
            'past_records' => $records,
            'sleeps_by_time' => $sleeps_by_time,
        ]);
    }

    private function getTimeBlock($time) {
        $timeObj = new Carbon($time);
        $blocks = [
            '00:00' => '00-06',
            '06:00' => '06-12',
            '12:00' => '12-18',
            '18:00' => '18-24',
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
