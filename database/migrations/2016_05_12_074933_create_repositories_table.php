<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepositoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repositories', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('slug', 70)->unique();
            $table->string('name', 64)->unique();
            $table->text('description')->nullable();
            $table->integer('repository_type_id')->nullable()->unsigned()->index();
            $table->integer('category_id')->nullable()->unsigned()->index();
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
        Schema::drop('repositories');
    }
}
