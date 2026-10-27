<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AppSection Section Event Container
    |--------------------------------------------------------------------------
    |
    | Event times are stored in UTC; emails show them in this timezone
    | since users have no timezone of their own yet.
    |
    */

    'display_timezone' => env('APP_DISPLAY_TIMEZONE', 'Europe/Kyiv'),
];
