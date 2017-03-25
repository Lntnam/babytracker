<?php
/**
 * Created by PhpStorm.
 * User: j2512
 * Date: 23/03/2017
 * Time: 08:22
 */

namespace App\Repositories;


class DiaperRepository
{
    public static function getPeeCount() {
        $date = Carbon::now()->toDateString();
    }
}