<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chapa API base URL
    |--------------------------------------------------------------------------
    |
    | Every Chapa API call — initializing a payment, verifying a transaction —
    | starts with this root URL. Test mode and live mode use the SAME base
    | URL. What changes between them is which secret key you send, not the URL.
    |
    */
    'base_url' => env('CHAPA_BASE_URL', 'https://api.chapa.co/v1'),

    /*
    |--------------------------------------------------------------------------
    | Chapa secret key
    |--------------------------------------------------------------------------
    |
    | Does two jobs: (1) authenticates every server-to-server request WE make
    | to Chapa, sent as "Authorization: Bearer {secret_key}", and (2) is the
    | same key Chapa used to sign webhooks — so we reuse it to verify that an
    | incoming webhook really came from Chapa and wasn't forged. One key, two
    | uses. Never sent to the browser, never committed to git.
    |
    */
    'secret_key' => env('CHAPA_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Chapa webhook secret
    |--------------------------------------------------------------------------
    |
    | A DIFFERENT value from secret_key -- configured separately, under
    | Settings > Webhooks in your Chapa dashboard, not the API tab. Chapa
    | signs every webhook body with THIS secret, not your API secret key.
    | This is the only value the webhook handler in file 7 checks against.
    |
    */
    'webhook_secret' => env('CHAPA_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Chapa public key
    |--------------------------------------------------------------------------
    |
    | // TODO: Phase 2 — only needed if we add Chapa's inline JS checkout
    | modal on the frontend instead of redirecting to their hosted page.
    | Not used by any file we're building today.
    |
    */
    'public_key' => env('CHAPA_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Chapa encryption key
    |--------------------------------------------------------------------------
    |
    | // TODO: Phase 2 — only needed for Chapa's Direct Charge API, where you
    | collect card/mobile-money details on your own form and encrypt them
    | yourself with 3DES before sending to Chapa. We're deliberately NOT doing
    | that — it pulls in PCI DSS obligations we don't need yet. Stored here
    | only so the config file matches what the dashboard actually gives you.
    |
    */
    'encryption_key' => env('CHAPA_ENCRYPTION_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Chapa settles in Ethiopian Birr only. Named here so "ETB" is never
    | hardcoded as a magic string in the Action, the Service, and the Request
    | separately.
    |
    */
    'currency' => 'ETB',

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    ngrok http 8000 --domain=psychologically-aprowl-tisa.ngrok-free.dev
    |--------------------------------------------------------------------------
    |
    | Chapa POSTs here after a payment attempt finishes. This is the ONLY
    | place we trust to confirm payment succeeded. In local development,
    | Chapa's servers can't reach "localhost" — you'll need a tunnel tool
    | like ngrok so Chapa can actually deliver this webhook to your machine.
    |
    */
    'callback_url' => env('CHAPA_CALLBACK_URL', '/payments/chapa/callback'),
    'webhook_url' => env('CHAPA_WEBHOOK_URL', '/webhooks/chapa'),

    /*
    |--------------------------------------------------------------------------
    | Return URL
    |--------------------------------------------------------------------------
    |
    | After checkout, Chapa redirects the RENTER'S BROWSER here. This is
    | purely cosmetic — where the human lands. It carries zero authority
    | over whether payment succeeded. Never mark a booking paid based on
    | this URL being hit.
    |
    */
    'return_url' => env('CHAPA_RETURN_URL', '/bookings'),
];
