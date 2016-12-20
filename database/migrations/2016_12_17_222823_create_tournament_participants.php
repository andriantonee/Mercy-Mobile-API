<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentParticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_participants', function (Blueprint $table) {
            $table->unsignedInteger('tournament_id');
            $table->unsignedInteger('teams_id');
            $table->timestamp('created_at');

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('teams_id')->references('id')->on('teams')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournament_participants');
    }
}
