<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('display_name', 255);
			$table->string('path', 255);
			$table->integer('size')->unsigned();
			$table->string('extension', 16); // Size is larger, but in this case we keep it like this
			$table->string('mime', 32)->nullable();
			$table->morphs('link');
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
		Schema::drop('files');
	}
}
