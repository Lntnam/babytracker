<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 22/03/2017
 * Time: 21:23
 */

namespace App\Repositories;

use App\Models\DayRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class WeightRepository
{
    public static function getCurrentWeight()
    {
        return VariableRepository::getCurrentValueByKey('weight');
    }

    public static function setCurrentWeight($value)
    {
        return VariableRepository::setCurrentValue('weight', (float)$value);
    }

    public static function getYesterdayWeight()
    {
        $record = DayRecordRepository::getDayRecord(Carbon::today()->subDay()->toDateString());

        if ($record) {
            return $record->weight;
        }
        return null;
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @return mixed
     */
    public static function getAverageWeight(Carbon $from, Carbon $to)
    {
        return DayRecord::whereBetween('day', [$from->toDateString(), $to->toDateString()])
            ->avg('weight');
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @return mixed
     */
    public static function getMinMaxWeight(Carbon $from, Carbon $to)
    {
        $result = DayRecord::whereBetween('day', [$from->toDateString(), $to->toDateString()])
            ->select(DB::raw('min(weight) min_w, max(weight) max_w'))
            ->first();
        return $result;
    }
}
