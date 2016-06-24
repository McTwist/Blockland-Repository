<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocklandUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	Schema::create('blockland_users', function (Blueprint $table)
	{
		$table->integer('id')->index();
		$table->string('name', 24)->index();
		$table->timestamps();
		$table->primary(array('id', 'name'));
	});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('blockland_users');
	}
}
