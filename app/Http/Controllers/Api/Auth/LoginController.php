<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Member;
use App\Models\OauthClient;
use App\Models\Other;
use Carbon;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

class LoginController extends AccessTokenController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  ServerRequestInterface $request
     * @return Response
     */
    public function index(ServerRequestInterface $request)
    {
        if (isset($request->getParsedBody()['grant_type'])) {
            if ($request->getParsedBody()['grant_type'] != 'password' && $request->getParsedBody()['grant_type'] != 'refresh_token') {
                return Other::response_json(400, 'The authorization grant type is not supported by the authorization server.');
            }
        }

        if (isset($request->getParsedBody()['client_id'])) {
            if (!OauthClient::id($request->getParsedBody()['client_id'])->exists()) {
                return Other::response_json(401, 'Client authentication failed.');
            }
        }

        $response = $this->withErrorHandling(function () use ($request) {
            return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        });

        $payload = json_decode($response->getBody()->__toString(), true);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            return Other::response_json($response->getStatusCode(), $payload['message']);
        }

        if (isset($payload['access_token'])) {
            $this->revokeOtherAccessTokens($payload);
        }

        $member = Member::find($request->getParsedBody()['username']);
        $member->last_login_at = Carbon::now();
        $member->save();

        return Other::response_json($response->getStatusCode(), 'Login success.', [
            'tokens' => $payload,
            'users' => [
                'username' => $member->username,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'phone' => $member->phone,
                'teams_id' => $member->teams_id,
                'status' => $member->status
            ]
        ]);
    }
}