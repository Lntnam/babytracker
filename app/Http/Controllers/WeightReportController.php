<?php

namespace App\Http\Controllers;

use App\Repositories\VariableRepository;
use App\Repositories\WeightRepository;
use App\Utilities;
use Carbon\Carbon;

class WeightReportController extends Controller
{
    public function index()
    {
        $dob = new Carbon(config('settings.baby_dob'));
        $age = $dob->diffInDays(Carbon::today());
        $weight_records = WeightRepository::getAllRecords();
        // convert weight records into key=>value array
        $weight_array = [];
        foreach ($weight_records as $record) {
            $weight_array[$dob->diffInDays(new Carbon($record->day))] = $record->weight;
        }
        $weight_frequency = VariableRepository::getPreferenceByKey('weight_frequency');
        $current_weight = WeightRepository::getCurrentWeight();

        $min_age = $weight_records->count() > 0 ? $dob->diffInDays(new Carbon($weight_records[0]->day)) : 0;
        $max_age = $weight_records->count() > 0 ? $dob->diffInDays(new Carbon($weight_records[count($weight_records)-1]->day)) : 0;
        $zscore_table = Utilities::getZScoreRange(
            $min_age - $weight_frequency,
            $max_age + $weight_frequency
        );
        $current_zscores = count($zscore_table) > 0 ? $zscore_table[$max_age] : 0;W

        $past_milestone = floor($age / 30) * 30;
        $next_milestone = ceil($age / 30) * 30;

        $next_zscore_milestone = Utilities::getZscore($next_milestone);
        if ($past_milestone == $next_milestone || $past_milestone == 0)
            $previous_zscore_milestone = null;
        else
            $previous_zscore_milestone = Utilities::getZscore($past_milestone);

        return view('weight', compact(
            'dob',
            'age',
            'weight_records',
            'weight_array',
            'min_age',
            'max_age',
            'past_milestone',
            'next_milestone',
            'weight_frequency',
            'zscore_table',
            'current_zscores',
            'current_weight',
            'previous_zscore_milestone',
            'next_zscore_milestone'
        ));
    }
}
