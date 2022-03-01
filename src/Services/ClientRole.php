<?php

namespace LaravelKeycloakAdmin\Services;

use LaravelKeycloakAdmin\Auth\ClientAuthService;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Arr;

class ClientRole extends Service
{

    /**
     * ClientRole constructor.
     * @param ClientAuthService $auth
     * @param ClientInterface $http
     */
    function __construct(ClientAuthService $auth , ClientInterface $http)
    {
        parent::__construct($auth, $http);

        $this->api = config('keycloakAdmin.api.client_roles');
    }

    /**
     * @param $response
     * @return bool
     */
    public function response($response)
    {
        if (!empty( $location = $response->getHeader('location') )){

            $url = current($location) ;

            // the current location url contains 
            // the client id like `.../clients/<CLIENT_ID>/roles/<ROLE_NAME>`
            // therefore the url is parsed and exploded
            $parts = parse_url($url);
            $path_parts=explode('/', $parts['path']);

            // get the client id from the current location url
            $client_id = $path_parts[array_search('clients', $path_parts) + 1];

            // get the role name from the current location url
            $role = substr( $url , strrpos( $url , '/') + 1 );

            return $this->getByName([
                'id' => $client_id,
                'role' => $role
            ]);
        }

        return json_decode($response->getBody()->getContents() , true) ?: true;
    }
}
