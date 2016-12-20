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

Route::put('/register', 'Api\Auth\RegisterController@index');
Route::post('/login', 'Api\Auth\LoginController@index');

Route::group([
    'middleware' => 'auth:api'
], function () {
    Route::post('/profile/update', 'Api\MemberController@update_profile');
    Route::post('/profile/password/update', 'Api\MemberController@update_password');
    Route::get('/profile/team/invitation', 'Api\MemberController@get_team_invitation');
    Route::post('/profile/team/invitation/accept', 'Api\MemberController@accept_team_invitation');
    Route::delete('/profile/team/invitation/reject', 'Api\MemberController@reject_team_invitation');

    Route::get('/team/detail', 'Api\TeamController@get_team_detail');
    Route::get('/team/member', 'Api\TeamController@get_team_member');
    Route::get('/team/member/pending-invitations', 'Api\TeamController@get_team_member_pending_invitations');
    Route::put('/team/create', 'Api\TeamController@create_team');
    Route::post('/team/detail/update', 'Api\TeamController@update_team_detail');
    Route::delete('/team/disband', 'Api\TeamController@disband_team');
    Route::get('/member/search', 'Api\TeamController@search_member');
    Route::put('/member/invite', 'Api\TeamController@invite_member');
    Route::delete('/member/invite/cancel', 'Api\TeamController@cancel_invite_member');

    Route::put('/tournament/{id}/register', 'Api\TournamentController@register_tournament');
    Route::get('/tournament/team', 'Api\TournamentController@get_team_tournament');
});

Route::get('/tournaments', 'Api\TournamentController@get_tournaments');
Route::get('/tournament/{id}/content', 'Api\TournamentController@get_tournament_content');
Route::get('/tournament/{id}/detail', 'Api\TournamentController@get_tournament_detail');

Route::post('/tournament/{id}/generate', 'Api\TournamentController@generate_tournament');
