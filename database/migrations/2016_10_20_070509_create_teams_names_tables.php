<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsNamesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams_names', function (Blueprint $table) {
            $table->string('name', 50);
            $table->bigInteger('created_at')->unsigned()->nullable();
            $table->bigInteger('updated_at')->unsigned()->nullable();

            $table->primary('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teams_names');
    }
}
