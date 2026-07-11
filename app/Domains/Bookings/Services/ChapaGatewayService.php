<?php

namespace App\Domains\Bookings\Services;

use App\Domains\Bookings\Exceptions\EscrowInitializationException;
use Illuminate\Support\Facades\Http;

class ChapaGatewayService
{
    private readonly string $secretKey;

    private readonly string $baseUrl;

    private readonly string $currency;

    public function __construct()
    {
        $this->secretKey = config('escrow.secret_key');
        $this->baseUrl = config('escrow.base_url');
        $this->currency = config('escrow.currency');
    }

    /**
     * Starts a Chapa payment. Chapa's job here is just to generate a
     * hosted checkout link — this method does NOT collect any card
     * details itself, doesn't touch OTPs, doesn't know anything about
     * Direct Charge. It sends the renter's details, gets back a URL,
     * hands that URL back so the Action can redirect the renter to it.
     *
     * $payload expects: amount, email, first_name, last_name,
     * phone_number, tx_ref (a unique string identifying this attempt —
     * the Action generates this, not us).
     */
    public function authorize(array $payload): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'amount' => $payload['amount'],
                'currency' => $this->currency,
                'email' => $payload['email'],
                'first_name' => $payload['first_name'],
                'last_name' => $payload['last_name'],
                'phone_number' => $payload['phone_number'],
                'tx_ref' => $payload['tx_ref'],
                'callback_url' => url(config('escrow.callback_url')),
                'return_url' => url(config('escrow.return_url')),
            ]);
        if ($response->failed() || $response->json('status') !== 'success') {
            throw EscrowInitializationException::gatewayDeclined(
                $payload['tx_ref'],
                $response->json('message') ?? 'Chapa did not return a message.'
            );
        }

        return [
            'checkout_url' => $response->json('data.checkout_url'),
            'tx_ref' => $payload['tx_ref'],
        ];
    }

    /**
     * Re-checks a transaction's real status directly with Chapa, by its
     * tx_ref. This exists because of one specific rule from Chapa's own
     * security docs: never trust a webhook notification alone — always
     * call this endpoint to confirm before treating a payment as real.
     * We'll call this from inside the webhook handler (file 7 == ChapaWebhookController), not from
     * the initial checkout flow.
     */
    public function verify(string $txRef): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$txRef}");

        if ($response->failed()) {
            throw EscrowInitializationException::gatewayDeclined(
                $txRef,
                $response->json('message') ?? 'Verification request failed.'
            );
        }

        return $response->json('data', []);
    }
}
