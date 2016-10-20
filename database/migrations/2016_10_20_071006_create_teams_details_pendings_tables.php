<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsDetailsPendingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams_details_pendings', function (Blueprint $table) {
            $table->integer('teams_id')->unsigned();
            $table->string('username', 20);
            $table->bigInteger('invited_at')->unsigned()->nullable();
            $table->bigInteger('requested_at')->unsigned()->nullable();

            $table->primary(['teams_id', 'username']);
            $table->foreign('teams_id')->references('id')->on('teams')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('username')->references('username')->on('members')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teams_details_pendings');
    }
}