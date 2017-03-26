<?php

namespace App\Http\Controllers;

use App\Repositories\DayRecordRepository;
use App\Repositories\SleepRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class SleepReportController extends Controller
{
    private $blocks = [
        'Early' => [1, 2, 3, 4, 5, 6],
        'Morning' => [7, 8, 9, 10, 11, 12],
        'Afternoon' => [13, 14, 15, 16, 17, 18],
        'Evening' => [19, 20, 21, 22, 23, 0],
    ];

    public function index()
    {
        // Past 10 days
        $records = DayRecordRepository::getPastRecords(10);

        // 10 days sleep analysis
        $sleeps = SleepRepository::getPastRecords(10);

        $analysis_by_block = [];
        foreach ($this->blocks as $key => $list) {
            $analysis_by_block[$key] = [
                'sleep_sum' => 0, // for percentage
                'awake_sum' => 0, // for percentage
                'sleep_list' => [],
                'awake_list' => [],

                'sleep_median' => 0,
                'awake_median' => 0,
            ];
        }

        $total_duration = 0;
        $total_awake = 0;
        $awake_time = null;

        foreach ($sleeps as $sleep) {
            $date = new Carbon($sleep->on);
            $start = $date->copy()->setTimeFromTimeString($sleep->sleep);
            $end = $date->copy()->setTimeFromTimeString($sleep->wake);
            if ($end <= $start) $end->addDay();
            $sleep_duration = $sleep->hours * 60 + $sleep->minutes;
            $total_duration += $sleep_duration;

            // Sleep duration by blocks
            $sleep_breakdown = $this->calculateBlockDuration($start->hour, $sleep_duration);
            foreach ($sleep_breakdown as $block) {
                $analysis_by_block[$block[0]]['sleep_sum'] += $block[1];
            }
            // Sleep duration list for median calculation
            $analysis_by_block[$sleep_breakdown[0][0]]['sleep_list'][] = $sleep_duration;

            // Awake
            if ($awake_time != null) {
                $awake_duration = $awake_time->diffInMinutes($start);
                $total_awake += $awake_duration;

                $awake_breakdown = $this->calculateBlockDuration($awake_time->hour, $awake_duration);
                foreach ($awake_breakdown as $block) {
                    $analysis_by_block[$block[0]]['awake_sum'] += $block[1];
                }
                $analysis_by_block[$awake_breakdown[0][0]]['awake_list'][] = $awake_duration;
            }
            $awake_time = $end->copy();
        }

        // analysis calculation
        foreach ($analysis_by_block as $key => $data) {
            $analysis_by_block[$key]['sleep_median'] = CarbonInterval::minutes(round($this->getArrayMedian($analysis_by_block[$key]['sleep_list'])));
            $analysis_by_block[$key]['awake_median'] = CarbonInterval::minutes(round($this->getArrayMedian($analysis_by_block[$key]['awake_list'])));
        }

        return view('sleep', [
            'past_records' => $records,
            'analysis' => $analysis_by_block,
        ]);
    }

    /**
     * @param int $start_hour
     * @param int $total_minutes
     * @return array
     */
    private function calculateBlockDuration($start_hour, $total_minutes) {
        foreach ($this->blocks as $key => $list) {
            $index = array_search($start_hour, $list);
            if ($index !== false) {
                if ($total_minutes <= (count($list) - $index) * 60) { // whole duration is within the time block
                    return [[$key, $total_minutes]];
                }
                else { // whole duration is split between several time blocks
                    $minutes = (count($list) - $index) * 60;
                    $result = [[$key, $minutes]];
                    $next_hour = Carbon::today()->addHour($start_hour + 1);
                    return array_merge($result, $this->calculateBlockDuration($next_hour->hour, $total_minutes - $minutes));
                }
            }
        }
        return null;
    }

    private function getArrayMedian($array) {
        $posA = floor(count($array) / 2);
        $posB = ceil(count($array) / 2);
        return ($array[$posA] + $array[$posB]) / 2;
    }
}
