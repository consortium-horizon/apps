<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStuffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stuffs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userID')->index()->unsigned();
            $table->integer('headID')->index()->unsigned();
            $table->integer('bodyID')->index()->unsigned();
            $table->integer('bootsID')->index()->unsigned();
            $table->integer('weaponID')->index()->unsigned();
            $table->integer('offhandID')->index()->unsigned();
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
        Schema::drop('stuffs');
    }
}
