<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddonCrcBlacklistTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 *  @return void
	 */
	public function up()
	{
		Schema::create('addon_crc_blacklist', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('crc')->unique();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('addon_crc_blacklist');
	}
}
