<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersGamesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members_games', function (Blueprint $table) {
            $table->string('username', 20);
            $table->integer('games_id')->unsigned();

            $table->primary(['username', 'games_id']);
            $table->foreign('username')->references('username')->on('members')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::drop('members_games');
    }
}
