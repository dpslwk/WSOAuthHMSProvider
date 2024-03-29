<?php

/**
 * Copyright 2022 Matt Lloyd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

use League\OAuth2\Client\Provider\GenericProvider;
use MediaWiki\User\UserIdentity;
use WSOAuth\AuthenticationProvider\AuthProvider;

class HMSAuthProvider implements AuthProvider
{
    /**
     * @var GenericProvider
     */
    private $provider;

    /**
     * HMSAuthProvider constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string|null $authUri
     * @param string|null $redirectUri
     */
    public function __construct( string $clientId, string $clientSecret, ?string $authUri, ?string $redirectUri )
    {
        $this->provider = new GenericProvider( [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
            'urlAuthorize' => rtrim($authUri, '/') . '/oauth/authorize',
            'urlAccessToken' => rtrim($authUri, '/') . '/oauth/token',
            'urlResourceOwnerDetails' => rtrim($authUri, '/') . '/api/users'
        ],
        // [
        //     'httpClient' => new GuzzleHttp\Client(['verify' => false]),
        // ]
        );
    }

    /**
     * Log in the user through the external OAuth provider.
     *
     * @param string|null &$key The consumer key returned by the OAuth provider. May be left empty.
     * @param string|null &$secret The consumer secret returned by the OAuth provider. May be left empty.
     * @param string|null &$authUrl The URL the user must be redirected to. Must not be left empty.
     * @return bool Returns true on successful login, false otherwise.
     * @internal
     */
    public function login( ?string &$key, ?string &$secret, ?string &$authUrl ): bool
    {

        $authUrl = $this->provider->getAuthorizationUrl( [
            // 'scope' => []
        ] );

        $secret = $this->provider->getState();

        // wfDebugLog( 'HMSAuthProvider', "login: key: $key secret: $secret auth_url: $auth_url " );
        return true;
    }

    /**
     * Log out the user and destroy the session.
     *
     * @param UserIdentity &$user
     * @return void
     * @internal
     */
    public function logout( UserIdentity &$user ): void
    {
        //
    }

    /**
     * Get user info from session. Returns false when the request failed or the user is not authorised.
     *
     * @param string $key The consumer key set during login().
     * @param string $secret The consumer secret set during login().
     * @param string &$errorMessage Message shown to the user when there is an error.
     * @return bool|array Returns an array with at least a 'name' when the user is authenticated, returns false when the user is not authorised or the authentication failed.
     * @internal
     */
    public function getUser( string $key, string $secret, &$errorMessage )
    {
        // wfDebugLog( 'HMSAuthProvider', "getUser: key: $key secret: $secret " );

        if ( !isset( $_GET['code'] ) ) {
            // wfDebugLog( 'HMSAuthProvider', "getUser: code " );

            return false;
        }

        if ( !isset( $_GET['state'] ) || empty( $_GET['state'] ) || ( $_GET['state'] !== $secret ) ) {
            // wfDebugLog( 'HMSAuthProvider', "getUser: state " );

            return false;
        }

        try {
            $token = $this->provider->getAccessToken( 'authorization_code', [
                'code' => $_GET['code']
            ] );
            // wfDebugLog( 'HMSAuthProvider', "getUser:token $token " );

            $user = $this->provider->getResourceOwner( $token )->toArray();
            $user = $user['data']; // need to unwrap the a level
            // wfDebugLog( 'HMSAuthProvider', "got User " );

            return [
                'name' => $user['username'],
                'realname' => $user['fullname'],
                'email' => $user['email']
            ];
        } catch ( \Exception $e ) {
            // wfDebugLog( 'HMSAuthProvider', 'getUser: catch ' . $e->getMessage() );

            return false;
        }
    }

    /**
     * Gets called whenever a user is successfully authenticated, so extra attributes about the user can be saved.
     *
     * @param int $id The ID of the User
     * @return void
     * @internal
     */
    public function saveExtraAttributes( int $id ): void
    {
        //
    }

    /**
     * Automatically add hms to the available auth providers.
     *
     * @param array &$auth_providers
     */
    public static function onWSOAuthGetAuthProviders( &$auth_providers )
    {
        $auth_providers['hms'] = HMSAuthProvider::class;
    }
}
