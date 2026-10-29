<?php

// Only overrides of the package defaults; Laravel merges in the rest.
return [
    // The UI authenticates through the session guard. The default `api` (Passport) guard
    // would be asked for the causer too, which fails in CLI commands without OAuth keys.
    'default_auth_driver' => 'web',
];
