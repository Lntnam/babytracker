<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 27/03/2017
 * Time: 00:06
 */

namespace App;


use Carbon\Carbon;

class Utilities
{
    private static $zscore_table;
    const ZSCORE_DELIMITER = "\t";

    public static function findArrayMedian($array) {
        if (empty($array)) return 0;
        if (count($array) < 3) return $array[0];

        sort($array);
        $posA = floor(count($array) / 2);
        $posB = ceil(count($array) / 2);
        return ($array[$posA] + $array[$posB]) / 2;
    }

    private static function loadWeightZscore() {
        $gender = config('settings.baby_gender');
        $path = storage_path('app/WHO_zscore_'.$gender.'.txt');
        if (file_exists($path)) {
            self::$zscore_table = file($path, FILE_SKIP_EMPTY_LINES);
        }
    }

    public static function getZScoreRange($from_age, $to_age) {
        if ($to_age >= $from_age) {
            if(empty(self::$zscore_table)) {
                self::loadWeightZscore();
            }
            $result = [];
            for ($i = $from_age + 1; $i <= $to_age + 1 && $i < count(self::$zscore_table); $i ++) {
                $line = str_getcsv(self::$zscore_table[$i], self::ZSCORE_DELIMITER);
                $result[array_shift($line)] = $line;
            }
            return $result;
        }
        return false;
    }

    public static function getZscore($age) {
        if(empty(self::$zscore_table)) {
            self::loadWeightZscore();
        }
        $line = str_getcsv(self::$zscore_table[$age + 1], self::ZSCORE_DELIMITER);
        array_shift($line);
        return $line;
    }

    public static function displayTimeDuration($start, $end) {
        $start_object = Carbon::today();
        $end_object = Carbon::today();

        if (is_string($start)) {
            $start_object->setTimeFromTimeString($start);
        } else {
            $start_object = $start;
        }
        if (is_string($end)) {
            $end_object->setTimeFromTimeString($end);
            if ($end_object->lt($start_object)) {
                $end_object->addDay();
            }
        } else {
            $end_object = $end;
        }

        $interval = $start_object->diffInMinutes($end_object);
        return floor($interval / 60) . 'h ' . ($interval % 60) . 'm';
    }

    public static function displayTimeString($input) {
        $time_object = Carbon::today();
        if (is_string($input)) {
            $time_object->setTimeFromTimeString($input);
        }
        else if (is_int($input)) {
            $time_object->setTime(floor($input / 60), $input % 60);
        }
        return $time_object->format('H:i');
    }
}
