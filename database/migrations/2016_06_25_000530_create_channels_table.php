<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('addon_id')->unsigned()->index();
            $table->tinyInteger('default')->default(0);
            $table->string('slug', 40)->unique();
            $table->string('name', 32);
            $table->text('description')->nullable();
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
        Schema::drop('channels');
    }
}
