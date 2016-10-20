<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsNamesGamesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams_names_games', function (Blueprint $table) {
            $table->integer('games_id')->unsigned();
            $table->string('teams_names_name', 50);
            $table->bigInteger('created_at')->unsigned()->nullable();
            $table->bigInteger('updated_at')->unsigned()->nullable();

            $table->primary(['games_id', 'teams_names_name']);
            $table->foreign('games_id')->references('id')->on('games')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('teams_names_name')->references('name')->on('teams_names')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teams_names_games');
    }
}
