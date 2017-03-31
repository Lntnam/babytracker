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

        // get WHO standards
        $age = $dob->diffInDays(Carbon::today());
        $next_milestone = ceil($age / 30);
        $WHO_table = config('static.WHO_weight_table');

        return view('weight', [
            'weight_weeks' => $weight_weeks,
            'records' => WeightRepository::getAllRecords(),
            'dob' => $dob,
            'weight' => WeightRepository::getCurrentWeight(),
            'next_milestone' => $next_milestone * 30,
            'WHO_table' => isset($WHO_table[$next_milestone]) ? $WHO_table[$next_milestone] : null,
        ]);
    }
}
