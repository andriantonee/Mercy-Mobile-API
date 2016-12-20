<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOauthRefreshTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_refresh_tokens', function (Blueprint $table) {
            $table->foreign('access_token_id')->references('id')->on('oauth_access_tokens')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_refresh_tokens', function (Blueprint $table) {
            $table->dropForeign('oauth_refresh_tokens_access_token_id_foreign');
        });
    }
}
