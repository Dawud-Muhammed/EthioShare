<?php

// This file is the very first thing Laravel runs when your app starts.
// In older Laravel versions, this setup was spread across several
// files (Kernel.php, various Provider files). Now it all lives in
// one place, right here.

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
// These four "use" lines just let us write short names like
// "Application" below, instead of the full, long name
// "Illuminate\Foundation\Application" every single time.

return Application::configure(basePath: dirname(__DIR__))
    // This starts building your app.
    // basePath tells Laravel where your project's root folder is.
    // dirname(__DIR__) means "go one folder above this file" — since
    // this file sits inside /bootstrap, one folder up IS your project root.

    ->withRouting(
        // This tells Laravel which files hold your routes, and what
        // KIND of routes each file contains.

        web: __DIR__.'/../routes/web.php',
        // "web" routes are for normal pages a person visits in a
        // browser. Protected by login SESSIONS (cookies) — this is
        // how your Livewire pages, like BookingShow, work.

        api: __DIR__.'/../routes/api.php',
        // "api" routes are JSON endpoints — meant to be called by
        // code (JavaScript, a mobile app), not typed into a browser
        // by a person. Protected by Sanctum TOKENS, not cookies.

        commands: __DIR__.'/../routes/console.php',
        // Terminal commands you run with "php artisan ..." live here.

        health: '/up',
        // Laravel automatically builds a simple page at "/up" that
        // just confirms "yes, the app is alive" — useful later for
        // uptime-monitoring tools.
    )

    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware = small checks that run BEFORE your actual page
        // loads. This closure is where you turn different checks on,
        // off, or adjust them — all in one place.

        $middleware->trustProxies(at: '*');
        // Your app sits behind ngrok right now. Ngrok talks to your
        // laptop over plain, unencrypted HTTP internally, even though
        // the person's browser is using HTTPS. Without this line,
        // Laravel wrongly believes the WHOLE connection is insecure,
        // and builds broken http:// links for things like your CSS
        // and JS — this is the exact bug we already fixed together.
        // "at: '*'" means "trust this info from any source" — fine
        // while developing with your own personal tunnel, but should
        // be narrowed to your real server's address once you deploy
        // for real users.

        $middleware->preventRequestForgery(except: [
            'webhooks/chapa',
        ]);
        // This turns on CSRF protection — a safety check that stops
        // OTHER websites from secretly submitting forms using a
        // visitor's logged-in session on YOUR site. It's on for every
        // web route by default. We carve out ONE exception: Chapa's
        // webhook — because that request comes from Chapa's own
        // server, not a browser with your session cookie, so it could
        // never pass this check anyway. We verify it's really Chapa a
        // different way instead (the signature check from file 7).
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        // This closure controls what happens when something goes
        // wrong (an exception is thrown) anywhere in your app.

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
        // Normally, an error shows a nice HTML error page — good for
        // a person browsing your site. But if the request's path
        // starts with "api/", we want a plain JSON error instead,
        // like {"message": "..."} — because whatever is calling your
        // API (JavaScript, a future mobile app) expects JSON back, not
        // an HTML page it has no way to display.
    })

    ->create();
    // This actually builds and hands back the finished Application
    // object. Nothing after this line does anything — it's the last step.