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
        'lifetime' => env('WORDPRESS_API_CACHE_LIFETIME', 360) //Cache lifetime (mins)
    ],
];
