<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('skillName');
            $table->integer('skillLevel');
            $table->string('slot');
            $table->integer('tier');
            $table->integer('level');
            $table->string('image');
            $table->integer('guildprice');
            $table->integer('marketprice');
            $table->boolean('twoHand');
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
        Schema::drop('items');
    }
}
