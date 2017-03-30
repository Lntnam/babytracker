<?php

use Illuminate\Database\Seeder;

class VariablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('variables')->insert([
            'name' => 'expectations',
            'value' => json_encode([
                'gram_per_day' => 40,
                'meal_per_day' => 480,
                'min_poop_per_day' => 3,
                'max_poop_per_day' => 4,
                'pee_per_day' => 6,
            ]),
        ]);

        DB::table('variables')->insert([
            'name' => 'currents',
            'value' => json_encode([
                'weight' => 0,
                'sleep_time' => 0,
                'meal' => 0,
                'date' => \Carbon\Carbon::today()->toDateString(),
            ]),
        ]);
    }
}
