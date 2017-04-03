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
            'name' => 'preferences',
            'value' => json_encode([
                'weight_frequency' => 2,
            ]),
        ]);

        DB::table('variables')->insert([
            'name' => 'currents',
            'value' => json_encode([
                'weight' => 0,
                'height' => 0,
                'date' => \Carbon\Carbon::today()->toDateString(),
            ]),
        ]);
    }
}
