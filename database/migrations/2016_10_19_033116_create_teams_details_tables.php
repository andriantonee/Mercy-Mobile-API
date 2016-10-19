<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsDetailsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams_details', function (Blueprint $table) {
            $table->integer('teams_id')->unsigned();
            $table->string('members_username', 20);
            $table->bigInteger('created_at')->unsigned();
            $table->bigInteger('updated_at')->unsigned();

            $table->primary(['teams_id', 'members_username']);
            $table->foreign('teams_id')->references('id')->on('teams')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::drop('teams_details');
    }
}