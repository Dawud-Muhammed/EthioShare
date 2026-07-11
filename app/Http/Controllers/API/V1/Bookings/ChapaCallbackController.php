<?php

namespace App\Http\Controllers\API\V1\Bookings;

use App\Domains\Bookings\Services\ConfirmChapaPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ChapaCallbackController
{
    public function __invoke(Request $request, ConfirmChapaPaymentService $confirmer): RedirectResponse
    {
        $txRef = $request->query('trx_ref')
            ?? $request->query('tx_ref')
            ?? $request->input('tx_ref');

        if (! $txRef) {
            return redirect()
                ->route('bookings.index')
                ->with('error', 'Missing Chapa transaction reference.');
        }

        if (! $confirmer->confirmByTxRef($txRef)) {
            return redirect()
                ->route('bookings.index')
                ->with('error', 'Payment was not verified.');
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Payment received.');
    }
}