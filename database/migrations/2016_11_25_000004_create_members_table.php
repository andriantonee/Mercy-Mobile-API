<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
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
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 20);
            $table->unsignedInteger('teams_id')->nullable();
            $table->unsignedTinyInteger('status')->default('0');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            $table->primary('username');
            $table->foreign('teams_id')->references('id')->on('teams')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('status')->references('id')->on('teams_member_status')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}