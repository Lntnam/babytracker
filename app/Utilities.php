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
    public static function findArrayMedian($array) {
        sort($array);
        $posA = floor(count($array) / 2);
        $posB = ceil(count($array) / 2);
        return ($array[$posA] + $array[$posB]) / 2;
    }
}