<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->string('username', 20);
            $table->string('password', 255);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->text('address');
            $table->string('phone', 20);
            $table->string('email', 255)->unique();
            $table->bigInteger('last_login_at')->unsigned()->nullable();
            $table->rememberToken();
            $table->bigInteger('created_at')->unsigned();
            $table->bigInteger('updated_at')->unsigned();

            $table->primary('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('members');
    }
}