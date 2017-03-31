<?php

namespace App\Http\Controllers;

use App\Repositories\WeightRepository;
use Carbon\Carbon;

class WeightReportController extends Controller
{
    public function index()
    {

        $dob = new Carbon(config('settings.baby_dob'));

        // weekly weight average
        $start = $dob->copy();
        $end = $dob->copy()->addDay(6);
        $week = 1;
        $weight_weeks = [];

        while (true) {
            $avg_weight = WeightRepository::getAverageWeight($start->toDateString(), $end->toDateString());

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
        $three_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(2)->toDateString(), Carbon::today()->toDateString());
        $seven_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(6)->toDateString(), Carbon::today()->toDateString());
        $fourteen_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(13)->toDateString(), Carbon::today()->toDateString());
        $thirty_days = WeightRepository::getMinMaxWeight(Carbon::today()->subDay(29)->toDateString(), Carbon::today()->toDateString());
        $increment_analysis = [
            '3D' => ($three_days->max_w - $three_days->min_w) / 3,
            '7D' => ($seven_days->max_w - $seven_days->min_w) / 7,
            '14D' => ($fourteen_days->max_w - $fourteen_days->min_w) / 14,
            '30D' => ($thirty_days->max_w - $thirty_days->min_w) / 30,
        ];

        return view('weight', [
            'weight_weeks' => $weight_weeks,
            'increment_analysis' => $increment_analysis,
            'records' => WeightRepository::getAllRecords(),
            'dob' => $dob,
        ]);
    }
}
