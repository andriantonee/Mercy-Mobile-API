<?php

namespace App\Http\Controllers\Api\Auth;

use DB;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Lcobucci\JWT\Parser as JwtParser;
use Zend\Diactoros\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\AuthorizationServer;

class LoginController extends AccessTokenController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  ServerRequestInterface  $request
     * @return Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        if (isset($request->getParsedBody()['grant_type']))
        {
            if ($request->getParsedBody()['grant_type'] != 'password' && $request->getParsedBody()['grant_type'] != 'refresh_token')
            {
                return response()->json(['error' => 'unsupported_grant_type', 'message' => 'The authorization grant type is not supported by the authorization server.', 'hint' => 'Check the `grant_type` parameter'], 400);
            }
        }

        if (isset($request->getParsedBody()['client_id']))
        {
            if (!DB::table('oauth_clients')->where('id', $request->getParsedBody()['client_id'])->exists())
            {
                return response()->json(['error' => 'invalid_client', 'message' => 'Client authentication failed'], 401);
            }
        }

        $response = $this->withErrorHandling(function () use ($request) {
            return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        });
        
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299)
        {
            return $response;
        }

        $payload = json_decode($response->getBody()->__toString(), true);

        if (isset($payload['access_token']))
        {
            $this->revokeOtherAccessTokens($payload);
        }

        return $response;
    }
}