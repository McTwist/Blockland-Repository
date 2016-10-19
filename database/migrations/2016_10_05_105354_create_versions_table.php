<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('versions', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('channel_id')->unsigned()->index();
			$table->tinyInteger('default')->default(0)->index();
			$table->string('name', 32);
			$table->text('change_log')->nullable();
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
		Schema::drop('versions');
	}
}
