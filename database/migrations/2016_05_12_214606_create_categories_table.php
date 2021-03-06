<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function (Blueprint $table)
		{
			// Identification
			$table->increments('id');

			// Attributes
			$table->string('name', 32)->unique();
			$table->string('icon', 32)->nullable();
			$table->integer('repository_type_id')->unsigned()->index();

			// Revision Tracking
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
		Schema::drop('categories');
	}
}
