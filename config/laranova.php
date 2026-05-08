<?php

return [

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

];
