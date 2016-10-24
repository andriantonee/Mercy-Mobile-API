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
    Route::post('/profile/update', 'Api\MemberController@update_profile');
    Route::post('/profile/password/change', 'Api\MemberController@change_password');
    Route::post('/team/create', 'Api\TeamController@create_team');
    Route::post('/team/{teams_id}/invite/user#{username}', 'Api\TeamController@invite_member_to_team');
    Route::post('/team/{teams_id}/accept', 'Api\TeamController@accept_team_invitation');
    Route::post('/team/{teams_id}/reject', 'Api\TeamController@reject_team_invitation');
    Route::post('/team/{teams_id}/join', 'Api\TeamController@member_join_to_team');
    Route::post('/team/{teams_id}/accept/user#{username}', 'Api\TeamController@accept_member_to_join');
    Route::post('/team/{teams_id}/reject/user#{username}', 'Api\TeamController@reject_member_to_join');
    Route::post('/team/{teams_id}/kick/user#{username}', 'Api\TeamController@kick_team_member');
    Route::post('/team/{teams_id}/leave', 'Api\TeamController@member_leave_team');
});

Route::post('/register', 'Api\Auth\RegisterController@register');
Route::post('/login', 'Api\Auth\LoginController@issueToken');