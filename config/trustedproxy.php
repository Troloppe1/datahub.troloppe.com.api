<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Trust forwarded headers from the reverse proxy / load balancer so URL
    | generation uses the public host and scheme instead of the internal one.
    | Set this to a comma-separated list of proxy IPs if you want to scope it
    | more tightly than trusting the immediate proxy hop.
    |
    */
    'proxies' => env('TRUSTED_PROXIES', '*'),
];
