<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('challonge_tournament_id')->nullable();
            $table->unsignedSmallInteger('games_id');
            $table->timestamp('registration_open');
            $table->timestamp('registration_close');
            $table->unsignedTinyInteger('member_participant_in_one_team');
            $table->string('location');
            $table->text('description');
            $table->text('rules');
            $table->timestamps();

            $table->foreign('games_id')->references('id')->on('games')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
}
