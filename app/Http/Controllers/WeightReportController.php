<?php

namespace App\Http\Controllers;

use App\Repositories\WeightRepository;
use Carbon\Carbon;

class WeightReportController extends Controller
{
    public function index() {

        $dob = new Carbon(config('settings.baby_dob'));

        // weekly weight average
        $start = $dob->copy();
        $end = $dob->copy()->addDay(6);
        $week = 1;
        $weight_weeks = [];

        while (true) {
            $avg_weight = WeightRepository::getAverageWeight($start, $end);

            if (!empty($avg_weight)) {
                $weight_weeks[$week] = round($avg_weight, 1);
            }
            $start = $end->copy()->addDay();
            $end = $start->copy()->addDay(6);
            $week++;
            if ($start->gte(Carbon::today()))
                break;
        }

        // increment analysis
        $three_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(2), Carbon::today());
        $seven_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(6), Carbon::today());
        $fourteen_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(13), Carbon::today());
        $thirty_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(29), Carbon::today());
        $increment_analysis = [
            '3D' => ($three_days->max_w - $three_days->min_w) / 3,
            '7D' => ($seven_days->max_w - $seven_days->min_w) / 7,
            '14D' => ($fourteen_days->max_w - $fourteen_days->min_w) / 14,
            '30D' => ($thirty_days->max_w - $thirty_days->min_w) / 30,
        ];

        // increment by week
        $start = $dob->copy();
        $end = $dob->copy()->addDay(6);
        $week = 1;
        $increment_weeks = [];

        while (true) {
            $week_weight = WeightRepository::getMinMaxWeight($start, $end);
            if (!empty($week_weight) && !empty($week_weight->min_w) && !empty($week_weight->max_w)) {
                $increment_weeks[$week] = $week_weight->max_w - $week_weight->min_w;
            }

            $start = $end->copy()->addDay();
            $end = $start->copy()->addDay(6);
            $week++;
            if ($start->gte(Carbon::today()))
                break;
        }

        return view('weight', [
            'weight_weeks' => $weight_weeks,
            'increment_analysis' => $increment_analysis,
            'increment_weeks' => $increment_weeks,
        ]);
    }
}
