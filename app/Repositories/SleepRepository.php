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
    public static function getCurrentSleep() {
        return VariableRepository::getCurrentValueByKey('sleep_time');
    }

    public static function setCurrentSleep($minutes) {
        $sleep = VariableRepository::getCurrentValueByKey('sleep_time');
        $sleep += $minutes;
        VariableRepository::setCurrentValue('sleep_time', $sleep);

        return $sleep;
    }

    public static function getSleepingRecord() {
        $sleep = Sleep::whereNull('wake')
            ->orderBy('id', 'desc')
            ->first();

        return $sleep;
    }

    public static function isSleeping() {
        return !empty(self::getSleepingRecord());
    }

    public static function addSleep($date, $time) {
        $sleep = New Sleep();
        $sleep->on = $date;
        $sleep->sleep = $time;

        return $sleep->save();
    }

    public static function wakeSleep($wake_time) {
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

            return self::setCurrentSleep($minutes);
        }

        return -1;
    }

    public static function endSleep() {
        $sleep = self::getSleepingRecord();
        if(!empty($sleep)) {
            $sleep_time = new Carbon($sleep->sleep);
            $wake_time = $sleep_time->copy()->endOfDay();
            self::wakeSleep($wake_time->toTimeString());

            $sleep_time = $wake_time->addDay()->startOfDay();
            self::addSleep($sleep_time->toDateString(), $sleep_time->toTimeString());
        }
    }

    public static function getPastRecords($quantity) {
        return Sleep::orderBy('on', 'desc')->take($quantity)->get();
    }
}
