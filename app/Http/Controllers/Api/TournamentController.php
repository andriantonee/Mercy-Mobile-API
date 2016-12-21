<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Other;
use App\Models\Team;
use App\Models\Tournament;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TournamentController extends Controller
{
    public function get_tournaments(Request $request)
    {
        $tournaments = Tournament::with([
            'game' => function ($game) {
                $game->select('games.id', 'games.name', 'games.image_url');
            }
        ])
            ->orderByRaw('IF(tournaments.registration_close >= ?, 1, 0) DESC', [Carbon::now()])
            ->orderBy('tournaments.registration_close', 'ASC')
            ->select('tournaments.id', 'tournaments.name', 'tournaments.games_id', 'tournaments.registration_open', 'tournaments.registration_close', 'tournaments.member_participant_in_one_team', 'tournaments.location', 'tournaments.description', 'tournaments.rules');
        if ($request->input('keyword')) {
            $tournaments->like_name($request->input('keyword'));
        }
        if ($request->input('game_category')) {
            $tournaments->game_category($request->input('game_category'));
        }
        $tournaments = $tournaments->get();

        $response_array = [];
        foreach ($tournaments as $tournament) {
            $response_array['tournaments'][] = [
                'id' => $tournament->id,
                'name' => $tournament->name,
                'category' => [
                    'id' => $tournament->game->id,
                    'name' => $tournament->game->name,
                    'image_url' => $tournament->game->image_url
                ],
                'registration_open' => strtotime($tournament->registration_open),
                'registration_close' => strtotime($tournament->registration_close),
                'member_participant_in_one_team' => $tournament->member_participant_in_one_team,
                'location' => $tournament->location,
                'description' => $tournament->description,
                'rules' => $tournament->rules
            ];
        }

        return Other::response_json(200, 'Get tournament success', $response_array);
    }

    public function get_tournament_content($id)
    {
        $tournament = Tournament::with([
            'game' => function ($game) {
                $game->select('games.id', 'games.name', 'games.image_url');
            }
        ])
            ->select('tournaments.id', 'tournaments.name', 'tournaments.games_id', 'tournaments.registration_open', 'tournaments.registration_close', 'tournaments.member_participant_in_one_team', 'tournaments.location', 'tournaments.description', 'tournaments.rules')
            ->find($id);

        $response_array['tournament'] = [
            'id' => $tournament->id,
            'name' => $tournament->name,
            'category' => [
                'id' => $tournament->game->id,
                'name' => $tournament->game->name,
                'image_url' => $tournament->game->image_url
            ],
            'registration_open' => strtotime($tournament->registration_open),
            'registration_close' => strtotime($tournament->registration_close),
            'member_participant_in_one_team' => $tournament->member_participant_in_one_team,
            'location' => $tournament->location,
            'description' => $tournament->description,
            'rules' => $tournament->rules
        ];

        $isRegisterOpen = false;
        if (time() >= $response_array['tournament']['registration_open'] && time() <= $response_array['tournament']['registration_close']) {
            $isRegisterOpen = true;
        }

        $response_array['isRegisterOpen'] = $isRegisterOpen;

        $isLeader = false;
        $isAlreadyRegister = false;

        if ($user = Auth::guard('api')->user()) {
            $isLeader = $user->status == 2;
            $isAlreadyRegister = $user->team->following_tournaments()->find($id) != null;
        }

        $response_array['isLeader'] = $isLeader;
        $response_array['isAlreadyRegister'] = $isAlreadyRegister;

        return Other::response_json(200, 'Get tournament content success', $response_array);
    }

    public function register_tournament($id, Request $request)
    {
        $rules = [
            'id' => 'required|integer|min:1|exists:tournaments,id'
        ];

        if ($res = Other::validate(['id' => $id], $rules)) {
            return $res;
        }

        if (!$team = $request->user()->team) {
            return Other::response_json(400, 'User do not have a team.');
        }

        if (!$request->user()->status == 2) {
            return Other::response_json(403, 'User is not a leader of the team.');
        }

        $tournament = Tournament::find($id);
        if (!$tournament->registration_open <= Carbon::now()) {
            return Other::response_json(400, 'This tournament registration is not open yet.');
        }

        if (!$tournament->registration_close >= Carbon::now()) {
            return Other::response_json(400, 'This tournament registration has been closed.');
        }

        try {
            $team->following_tournaments()->attach($id, [
                'created_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {
            return Other::response_json(400, 'Your team already registered this tournament.');
        }

        return Other::response_json(200, 'Your team has register this tournament successfully.');
    }

    public function get_team_tournament(Request $request)
    {
        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        $tournaments = Team::find($teams_id)->following_tournaments()->with([
            'game' => function ($game) {
                $game->select('games.id', 'games.name', 'games.image_url');
            }
        ])
            ->orderByRaw('IF(tournaments.registration_close >= ?, 1, 0) DESC', [Carbon::now()])
            ->orderBy('tournaments.registration_close', 'ASC')
            ->select('tournaments.id', 'tournaments.name', 'tournaments.games_id', 'tournaments.registration_open', 'tournaments.registration_close', 'tournaments.member_participant_in_one_team', 'tournaments.location', 'tournaments.description', 'tournaments.rules')
            ->get();

        $response_array = [];
        foreach ($tournaments as $tournament) {
            $response_array['tournaments'][] = [
                'id' => $tournament->id,
                'name' => $tournament->name,
                'category' => [
                    'id' => $tournament->game->id,
                    'name' => $tournament->game->name,
                    'image_url' => $tournament->game->image_url
                ],
                'registration_open' => strtotime($tournament->registration_open),
                'registration_close' => strtotime($tournament->registration_close),
                'member_participant_in_one_team' => $tournament->member_participant_in_one_team,
                'location' => $tournament->location,
                'description' => $tournament->description,
                'rules' => $tournament->rules
            ];
        }

        return Other::response_json(200, 'Get tournament success', $response_array);
    }

    public function generate_tournament($id)
    {
        $rules = [
            'id' => 'required|integer|min:1|exists:tournaments,id'
        ];

        if ($res = Other::validate(['id' => $id], $rules)) {
            return $res;
        }

        $tournament = Tournament::find($id);
        $teams = $tournament->participant;
        if (count($teams->toArray()) < 4) {
            return Other::response_json(400, 'Tournament cannot be start because the participant is less than 4.');
        }

        $client = new \GuzzleHttp\Client();

        try {
            if ($tournament->challonge_tournament_id) {
                $tournament_res = $client->request('GET', 'https://api.challonge.com/v1/tournaments/' . $tournament->challonge_tournament_id . '.json', [
                    'query' => [
                        'api_key' => env('CHALLONGE_API_KEY', "")
                    ]
                ]);
            } else {
                $tournament_res = $client->request('POST', 'https://api.challonge.com/v1/tournaments.json', [
                    'form_params' => [
                        'api_key' => env('CHALLONGE_API_KEY', ""),
                        'tournament' => [
                            'name' => substr($tournament->name, 0, 60),
                            'tournament_type' => 'single elimination',
                            'url' => 'tournamensia_' . $tournament->id . '_' . time(),
                            'open_signup' => false,
                            'hold_third_place_match' => true,
                            'ranked_by' => 'match wins',
                            'hide_forum' => true,
                            'show_rounds' => true,
                            'private' => true
                        ]
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        if (!($tournament_res->getStatusCode() >= 200 && $tournament_res->getStatusCode() <= 299)) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        $tournament_arr = json_decode($tournament_res->getBody(), true)['tournament'];

        if (!$tournament->challonge_tournament_id) {
            $tournament->challonge_tournament_id = $tournament_arr['id'];
            $tournament->save();
        }

        foreach ($teams as $team) {
            try {
                $participant_res = $client->request('POST', 'https://api.challonge.com/v1/tournaments/' . $tournament->challonge_tournament_id . '/participants.json', [
                    'form_params' => [
                        'api_key' => env('CHALLONGE_API_KEY', ""),
                        'participant' => [
                            'name' => $team->name . ' - ' . $team->id,
                            'misc' => $team->id
                        ]
                    ]
                ]);
            } catch (\Exception $e) {
            }
        }

        try {
            $participant_res = $client->request('GET', 'https://api.challonge.com/v1/tournaments/' . $tournament->challonge_tournament_id . '/participants.json', [
                'query' => [
                    'api_key' => env('CHALLONGE_API_KEY', "")
                ]
            ]);
        } catch (\Exception $e) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        if (!($participant_res->getStatusCode() >= 200 && $participant_res->getStatusCode() <= 299)) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        $participant_arr = json_decode($participant_res->getBody(), true);

        if (!count($teams->toArray()) == count($participant_arr)) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        try {
            $randomize_res = $client->request('POST', 'https://api.challonge.com/v1/tournaments/' . $tournament->challonge_tournament_id . '/participants/randomize.json', [
                'form_params' => [
                    'api_key' => env('CHALLONGE_API_KEY', "")
                ]
            ]);
        } catch (\Exception $e) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        if (!($randomize_res->getStatusCode() >= 200 && $randomize_res->getStatusCode() <= 299)) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        try {
            $tournament_start_res = $client->request('POST', 'https://api.challonge.com/v1/tournaments/' . $tournament->challonge_tournament_id . '/start.json', [
                'form_params' => [
                    'api_key' => env('CHALLONGE_API_KEY', "")
                ]
            ]);
        } catch (\Exception $e) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        if (!($tournament_start_res->getStatusCode() >= 200 && $tournament_start_res->getStatusCode() <= 299)) {
            return Other::response_json('500', 'Something went wrong. Please try again');
        }

        return Other::response_json(200, 'Your tournament has been generated successfully');
    }

    public function get_tournament_detail($id)
    {
        $rules = [
            'id' => 'required|integer|min:1|exists:tournaments,id'
        ];

        if ($res = Other::validate(['id' => $id], $rules)) {
            return $res;
        }

        $client = new \GuzzleHttp\Client();

        $tournament = Tournament::find($id);

        $match = [];
        $match_round = [];
        if ($tournament->challonge_tournament_id) {
            try {
                $participant_res = $client->request('GET', 'https://api.challonge.com/v1/tournaments/' . $tournament->challonge_tournament_id . '/participants.json', [
                    'query' => [
                        'api_key' => env('CHALLONGE_API_KEY', "")
                    ]
                ]);
            } catch (\Exception $e) {
                return Other::response_json('500', 'Something went wrong. Please try again');
            }

            if (!($participant_res->getStatusCode() >= 200 && $participant_res->getStatusCode() <= 299)) {
                return Other::response_json('500', 'Something went wrong. Please try again');
            }

            $participant_arr = json_decode($participant_res->getBody(), true);
            $teams = $tournament->participant->keyBy('id');

            $participant = [];
            foreach ($participant_arr as $value) {
                $tmp_participant = $value['participant'];
                $participant[$tmp_participant['id']] = $teams[(int)$tmp_participant['misc']]->name;
            }

            try {
                $match_res = $client->request('GET', 'https://api.challonge.com/v1/tournaments/' . $tournament->challonge_tournament_id . '/matches.json', [
                    'query' => [
                        'api_key' => env('CHALLONGE_API_KEY', "")
                    ]
                ]);
            } catch (\Exception $e) {
                return Other::response_json('500', 'Something went wrong. Please try again');
            }

            if (!($match_res->getStatusCode() >= 200 && $match_res->getStatusCode() <= 299)) {
                return Other::response_json('500', 'Something went wrong. Please try again');
            }

            $match_arr = json_decode($match_res->getBody(), true);

            $max_round = 0;
            foreach ($match_arr as $value) {
                $tmp_match = $value['match'];

                $tmp_match_data = [
                    'player_1' => 'TBD',
                    'player_2' => 'TBD',
                    'player_1_score' => 0,
                    'player_2_score' => 0,
                    'has_winner' => false,
                    'player_1_win' => false,
                    'player_2_win' => false
                ];

                if ($tmp_match['player1_id']) {
                    $tmp_match_data['player_1'] = $participant[$tmp_match['player1_id']];
                }

                if ($tmp_match['player2_id']) {
                    $tmp_match_data['player_2'] = $participant[$tmp_match['player2_id']];
                }

                if ($tmp_match['scores_csv']) {
                    $score_arr = explode('-', $tmp_match['scores_csv']);
                    $tmp_match_data['player_1_score'] = $score_arr[0];
                    $tmp_match_data['player_2_score'] = $score_arr[1];
                }

                if ($tmp_match['winner_id']) {
                    $tmp_match_data['has_winner'] = true;
                    if ($tmp_match['winner_id'] == $tmp_match['player1_id']) {
                        $tmp_match_data['player_1_win'] = true;
                    } else {
                        $tmp_match_data['player_2_win'] = true;
                    }
                }

                $tmp_match_data['scheduled_time'] = $tmp_match['scheduled_time'] ? strtotime($tmp_match['scheduled_time']) : 0;

                $match[$tmp_match['round']][] = $tmp_match_data;
                if ($max_round < $tmp_match['round']) {
                    $max_round = $tmp_match['round'];
                }
            }

            foreach ($match as $key => $value) {
                $round_name = 'Round ' . ($key);
                if ($max_round == 1) {
                    if ($key == 1) {
                        $round_name = 'Finals';
                    }
                } else if ($max_round >= 2) {
                    if ($key == $max_round - 1) {
                        $round_name = 'Semifinals';
                    } else if ($key == $max_round) {
                        $round_name = 'Finals';
                    }
                }

                if ($key == 0) {
                    $round_name = "Bronze Match";
                }

                $match_round[$key] = $round_name;
            }
        }

        return Other::response_json(200, 'Get tournament detail success', [
            'tournament_name' => $tournament->name,
            'match' => $match,
            'match_round' => $match_round
        ]);
    }
}
