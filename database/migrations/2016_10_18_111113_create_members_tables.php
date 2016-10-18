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
            $table->string('last_name', 100)->nullable();
            $table->text('address');
            $table->string('phone', 20);
            $table->string('email', 255)->unique();
            $table->bigInteger('last_login_at')->nullable();
            $table->rememberToken();
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');

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
