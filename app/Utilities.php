<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 27/03/2017
 * Time: 00:06
 */

namespace App;


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
}
