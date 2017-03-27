<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 23/03/2017
 * Time: 08:21
 */

namespace App\Repositories;

use App\Models\Sleep;
use Carbon\Carbon;

class SleepRepository
{
    public static function getTodayTotalSleepAmount()
    {
        return VariableRepository::getCurrentValueByKey('sleep_time');
    }

    public static function setTodayTotalSleepAmount($minutes)
    {
        $sleep = VariableRepository::getCurrentValueByKey('sleep_time');
        $sleep += $minutes;
        VariableRepository::setCurrentValue('sleep_time', $sleep);

        return $sleep;
    }

    public static function getCurrentSleepingRecord()
    {
        $sleep = Sleep::whereNull('wake')
            ->orderBy('id', 'desc')
            ->first();

        return $sleep;
    }

    public static function isSleeping()
    {
        return !empty(self::getCurrentSleepingRecord());
    }

    public static function addSleep($date, $time)
    {
        $sleep = New Sleep();
        $sleep->on = $date;
        $sleep->sleep = $time;

        return $sleep->save();
    }

    public static function wakeSleep($wake_time)
    {
        $sleep = Sleep::whereNull('wake')
            ->orderBy('id', 'desc')
            ->first();
        if ($sleep) {
            $sleep->wake = $wake_time;

            $wake_time = new Carbon($wake_time);
            $sleep_time = new Carbon($sleep->sleep);
            if ($sleep_time->gt($wake_time)) $sleep_time->subDay();

            $minutes = $sleep_time->diffInMinutes($wake_time);
            $sleep->hours = floor($minutes / 60);
            $sleep->minutes = $minutes % 60;

            $sleep->save();

            return self::setTodayTotalSleepAmount($minutes);
        }

        return -1;
    }

    public static function splitSleep()
    {
        $sleep = self::getCurrentSleepingRecord();
        if (!empty($sleep)) {
            $sleep_time = new Carbon($sleep->sleep);
            $wake_time = $sleep_time->copy()->endOfDay();
            self::wakeSleep($wake_time->toTimeString());

            $sleep_time = $wake_time->addDay()->startOfDay();
            self::addSleep($sleep_time->toDateString(), $sleep_time->toTimeString());
        }
    }

    public static function getPastRecords($no_of_days)
    {
        $date = Carbon::today()->subDay($no_of_days - 1);
        return Sleep::where('on', '>=', $date->toDateString())
            ->whereNotNull('wake')
            ->orderBy('on', 'asc')
            ->orderBy('sleep', 'asc')
            ->get();
    }

    public static function getLatestSleep() {
        return Sleep::whereNotNull('wake')
            ->orderBy('on', 'desc')
            ->orderBy('sleep', 'desc')
            ->first();
    }

    public static function deleteCurrentSleeping() {
        $sleep = Sleep::whereNull('wake')
            ->orderBy('id', 'desc')
            ->first();

        if ($sleep) $sleep->delete();
    }
}
