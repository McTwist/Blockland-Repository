<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelUserTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('channel_user', function (Blueprint $table)
		{
			$table->integer('user_id')->unsigned()->index();
			$table->integer('channel_id')->unsigned()->index();
			$table->string('token', 60);
			$table->primary(['user_id', 'channel_id']);
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
		Schema::drop('channel_user');
	}
}
