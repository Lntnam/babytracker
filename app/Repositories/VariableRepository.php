<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 22/03/2017
 * Time: 21:25
 */

namespace App\Repositories;

use App\Models\Variable;

class VariableRepository
{
    public static function getExpectationByKey($key) {
        $var = Variable::where([['name', 'expectations']])
            ->first();
        if (!empty($var)) {
            $var_array = json_decode($var->value);
            return $var_array->$key;
        }
        return null;
    }

    public static function getCurrentValueByKey($key) {
        $var = Variable::where([['name', 'currents']])
            ->first();
        if (!empty($var)) {
            $var_array = json_decode($var->value);

            return $var_array->$key;
        }
        return null;
    }

    public static function setExpectation($key, $value) {
        $var = Variable::where([['name', 'expectations']])
            ->first();
        if (!empty($var)) {
            $var_array = json_decode($var->value);
            $var_array->$key = $value;
            $var->value = json_encode($var_array);
            $var->save();
        }
    }

    public static function setCurrentValue($key, $value) {
        $var = Variable::where([['name', 'currents']])
            ->first();

        if (!empty($var)) {
            $var_array = json_decode($var->value);
            $var_array->$key = $value;
            $var->value = json_encode($var_array);
            return $var->save();
        }
        return false;
    }

    public static function clearCurrentValues() {
        // self::setCurrentValue('weight', 0);
        self::setCurrentValue('sleep_time', 0);
        self::setCurrentValue('meal', 0);
    }
}
