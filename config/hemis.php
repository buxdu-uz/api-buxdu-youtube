<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hemis Configure file
    |--------------------------------------------------------------------------
    */
    'host' => 'https://student.buxdu.uz/rest/v1/',
    'api_key' => 'noygajLPy3X7g3dwz3jp76iisR56Prtl',
    'limit' => 100,

    'hemis' => [
        'client_id' => env('HEMIS_CLIENT_ID', '8'),
        'client_secret' => env('HEMIS_CLIENT_SECRET', 'Vt5dnZtzK_v3vzs0ycsV2uLzrh7zicZUrz4TEiOI'),
        'redirect' => env('HEMIS_REDIRECT_URI', 'http://hemis-oauth-test.lc/callback'),
        'authorize_url' => 'https://hemis.buxdu.uz/oauth/authorize',
        'token_url' => 'https://hemis.buxdu.uz/oauth/access-token',
        'resource_url' => 'https://hemis.buxdu.uz/oauth/api/user?fields=id,uuid,type,name,login,picture,email,university_id,phone',
    ],
];
