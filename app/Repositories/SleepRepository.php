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
        $today = DayRecordRepository::getCurrentDate();
        $sleeps = self::getSleepsOnDate($today);
        $total = 0;
        foreach ($sleeps as $sleep) {
            $total += $sleep->hours * 60 + $sleep->minutes;
        }
        return $total;
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

    public static function addSleep($time)
    {
        $today = DayRecordRepository::getCurrentDate();

        $sleep = New Sleep();
        $sleep->on = $today;
        $sleep->sleep = $time;

        $sleep->save();
        return $sleep;
    }

    public static function wakeSleep($wake_time)
    {
        $sleep = self::getCurrentSleepingRecord();

        if ($sleep) {
            $sleep->wake = $wake_time;

            $wake_time = new Carbon($wake_time);
            $sleep_time = new Carbon($sleep->sleep);
            if ($sleep_time->gte($wake_time)) $sleep_time->subDay();

            $minutes = $sleep_time->diffInMinutes($wake_time);
            $sleep->hours = floor($minutes / 60);
            $sleep->minutes = $minutes % 60;

            $sleep->save();
            return $sleep;
        }
        return null;
    }

    public static function getPastRecords($no_of_days)
    {
        $today = new Carbon(DayRecordRepository::getCurrentDate());
        $date = $today->copy()->subDay($no_of_days - 1);

        return Sleep::whereBetween('on', [$date->toDateString(), $today->toDateString()])
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

    public static function getSleepsOnDate($date)
    {
        return Sleep::where('on', $date)
            ->whereNotNull('wake')
            ->orderBy('sleep', 'asc')->get();
    }
}
