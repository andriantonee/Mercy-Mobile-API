<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'auth:api'
], function () {
	Route::get('/user', function(Request $request) {
		return $request->user();
	});
    Route::post('/team/create', 'Api\TeamController@create_team');
    Route::post('/team/{teams_id}/invite', 'Api\TeamController@invite_members_to_team');
});

Route::post('/register', 'Api\Auth\RegisterController@register');
Route::post('/login', 'Api\Auth\LoginController@issueToken');