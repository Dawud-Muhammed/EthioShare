<?php

namespace App\Http\Controllers\API\V1\Bookings;

use App\Domains\Bookings\Services\ConfirmChapaPaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ChapaWebhookController extends Controller
{
    public function __invoke(Request $request, ConfirmChapaPaymentService $confirmer): Response
    {
        if (! $this->hasValidSignature($request)) {
            Log::warning('Chapa webhook rejected: signature mismatch.');
            abort(401, 'Invalid signature.');
        }

        $txRef = $request->input('tx_ref') ?? $request->input('trx_ref');

        if (! $txRef) {
            // Nothing useful to act on. We return 200 anyway -- returning
            // an error here would make Chapa retry a request that will
            // fail identically every time. A 2xx just tells Chapa "seen,
            // stop resending" -- the Log::warning is what actually gets
            // you to investigate.
            Log::warning('Chapa webhook received with no tx_ref.');

            return response('ignored', 200);
        }

        $confirmer->confirmByTxRef($txRef);

        return response('ok', 200);
    }

    private function hasValidSignature(Request $request): bool
    {
        // getContent() gets the EXACT raw bytes Chapa sent, before
        // Laravel parses them into an array. This matters: if we
        // instead re-built a "body" from $request->all() and hashed
        // THAT, PHP might order keys differently or format numbers
        // differently than Chapa's original bytes did -- and the hash
        // would never match, even for a completely legitimate webhook.
        // Signatures are computed over exact bytes, not equivalent data.
        $expected = hash_hmac('sha256', $request->getContent(), config('escrow.webhook_secret'));

        // hash_equals(), not ==. A plain string comparison exits the
        // instant it finds a mismatched character -- which means it
        // takes measurably longer to compare "close" guesses than
        // wrong ones. An attacker can, in theory, use that timing
        // difference to guess the correct signature one byte at a
        // time. hash_equals() always takes the same amount of time
        // regardless of how much of the string matches.
        return hash_equals($expected, (string) $request->header('x-chapa-signature'));
    }
}
