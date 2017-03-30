<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_records', function (Blueprint $table) {
            $table->timestamps();
            $table->date('day');
            $table->integer('sleep');
            $table->integer('meal');
            $table->decimal('weight')->nullable();
            $table->integer('height')->nullable();

            $table->primary('day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day_records');
    }
}
