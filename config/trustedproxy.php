<?php

return [
    /*
    | Proxies whose X-Forwarded-* headers are trusted. Leave empty on ordinary
    | shared hosting (cPanel), where visitors connect to the server directly:
    | trusting the headers there would let anyone fake their IP address.
    | Set TRUSTED_PROXIES=* on platforms that sit behind a load balancer (Render).
    */
    'proxies' => env('TRUSTED_PROXIES'),
];
