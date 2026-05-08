<?php

return [

    /*
    | Default headers pre-populated in the request builder UI.
    | Users can override or remove them before sending.
    */
    'default_headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ],

    /*
    | Whether to auto-add Content-Type: application/json when the request
    | has a body and no Content-Type header is explicitly set.
    */
    'auto_content_type' => true,

    'faker' => [
        'locale' => env('LARANOVA_FAKER_LOCALE', 'en_US'),
    ],

    'http' => [
        'timeout' => env('LARANOVA_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('LARANOVA_HTTP_CONNECT_TIMEOUT', 10),
    ],

    'history' => [
        'max_items' => env('LARANOVA_HISTORY_MAX', 100),
    ],

    'routes' => [
        'exclude_prefixes' => [
            'laranova',
            'request-docs',
            '_debugbar',
            '_ignition',
            'telescope',
            'sanctum',
        ],
    ],

    /*
    | Default security scheme pre-populated in the request builder UI.
    | type: null, bearer, basic, apikey, jwt
    | name: parameter name (used for apikey/jwt)
    | position: header, query, cookie (used for apikey/jwt)
    */
    'security' => [
        'type' => env('LARANOVA_SECURITY_TYPE', 'bearer'),
        'name' => env('LARANOVA_SECURITY_NAME', 'api_key'),
        'position' => env('LARANOVA_SECURITY_POSITION', 'header'),
    ],

    /*
    | Default variables pre-populated in the Variables panel.
    | Users can override or remove them. Set via pm.variables.set() in pre-scripts.
    */
    'default_variables' => [
        'baseUrl' => env('LARANOVA_DEFAULT_BASE_URL', 'http://pinsever.test'),
        'bearerToken' => '',
    ],

];
