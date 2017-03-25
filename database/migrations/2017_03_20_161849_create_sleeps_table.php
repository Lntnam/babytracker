<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSleepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sleeps', function (Blueprint $table) {
            $table->increments('id');
            $table->date('on');
            $table->time('sleep');
            $table->time('wake')->nullable();
            $table->integer('hours')->default(0);
            $table->integer('minutes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sleeps');
    }
}
