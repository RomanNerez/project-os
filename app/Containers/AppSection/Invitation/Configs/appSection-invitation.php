<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Invitation Link Lifetime
    |--------------------------------------------------------------------------
    |
    | In days. After this period the invitation link can no longer be used.
    |
    */
    'expires-in-days' => env('INVITATION_EXPIRES_DAYS', 7),
];
