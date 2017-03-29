<?php

/**
 * |--------------------------------------------------------------------------
 * | API configurations
 * |
 * | Config the API endpoints and any authentication settings here and pull
 * | them in from the relevant .env files
 * |--------------------------------------------------------------------------
 * |
 */

return [
    'wordpress' => [
        'endpoint' => env('WORDPRESS_API_ENDPOINT', 'https://reqres.in/api/'),
        'timeout' => env('WORDPRESS_API_CACHETIME', 360) //Cache lifetime (mins)
    ],
];
