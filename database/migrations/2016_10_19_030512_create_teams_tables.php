<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('games_id')->unsigned();
            $table->string('members_username', 20);
            $table->string('name', 50);
            $table->bigInteger('created_at')->unsigned();
            $table->bigInteger('updated_at')->unsigned();

            $table->foreign('games_id')->references('id')->on('games')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('members_username')->references('username')->on('members')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teams');
    }
}