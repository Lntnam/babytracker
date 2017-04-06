<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 22/03/2017
 * Time: 21:23
 */

namespace App\Repositories;

use App\Models\DayRecord;
use Illuminate\Support\Facades\DB;

class WeightRepository
{
    public static function getCurrentWeight()
    {
        return VariableRepository::getCurrentValueByKey('weight');
    }

    public static function getCurrentHeight()
    {
        return VariableRepository::getCurrentValueByKey('height');
    }

    public static function setCurrentWeight($value)
    {
        return VariableRepository::setCurrentValue('weight', (float)$value);
    }

    public static function setCurrentHeight($value)
    {
        return VariableRepository::setCurrentValue('height', (int)$value);
    }

    public static function updateWeightHeight($weight, $height)
    {
        return DayRecordRepository::createUpdateDayRecord(null, null, $weight, $height);
    }

    public static function getAverageWeight($from, $to)
    {
        return DayRecord::whereBetween('day', [$from, $to])
            ->avg('weight');
    }

    public static function getMinMaxWeight($from, $to)
    {
        $result = DayRecord::whereBetween('day', [$from, $to])
            ->select(DB::raw('min(weight) min_w, max(weight) max_w'))
            ->first();
        return $result;
    }

    public static function getAllRecords()
    {
        return DayRecord::whereNotNull('weight')
            ->orderBy('day', 'asc')
            ->get();
    }

    public static function getLatestRecord() {
        return DayRecord::whereNotNull('weight')
            ->orderBy('day', 'desc')
            ->first();
    }
}
