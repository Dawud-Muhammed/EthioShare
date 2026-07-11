<?php

namespace App\Domains\Bookings\Services;

use App\Domains\Bookings\Events\BookingPaymentConfirmed;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Domains\Shared\Enums\Booking\EscrowStatusEnum;
use App\Domains\Shared\Enums\Booking\PaymentStatusEnum;
use App\Domains\Shared\Enums\EscrowLedger\EscrowLedgerStatus;
use App\Models\EscrowLedger;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfirmChapaPaymentService
{
    public function __construct(
        private readonly ChapaGatewayService $gateway,
    ) {}

    /**
     * Confirms a payment attempt by tx_ref and updates both ledger + booking.
     *
     * Returns true only when the booking state is moved to paid/confirmed.
     */
    public function confirmByTxRef(string $txRef): bool
    {
        $ledger = EscrowLedger::where('transaction_id', $txRef)->first();

        if (! $ledger) {
            Log::warning("Chapa confirmation for unrecognized tx_ref: {$txRef}");

            return false;
        }

        return (bool) DB::transaction(function () use ($ledger, $txRef) {
            $lockedLedger = EscrowLedger::lockForUpdate()->find($ledger->id);

            if (! $lockedLedger) {
                return false;
            }

            // Idempotency: if this tx_ref was already processed, stop.
            if ($lockedLedger->status !== EscrowLedgerStatus::PENDING_PAYMENT) {
                return false;
            }

            try {
                $verified = $this->gateway->verify($txRef);
            } catch (Throwable $e) {
                Log::warning('Chapa verification failed during confirmation.', [
                    'tx_ref' => $txRef,
                    'error' => $e->getMessage(),
                ]);

                return false;
            }

            if (($verified['status'] ?? null) !== 'success') {
                return false;
            }

            $lockedLedger->update([
                'status' => EscrowLedgerStatus::FUNDED,
                'amount_held' => $lockedLedger->amount_due,
                'gateway_transaction_id' => $verified['reference'] ?? null,
                'transactions' => [
                    ...($lockedLedger->transactions ?? []),
                    [
                        'type' => 'AUTHORIZE',
                        'amount' => $lockedLedger->amount_due,
                        'status' => 'SUCCESS',
                        'timestamp' => now()->toIso8601String(),
                    ],
                ],
            ]);

            $booking = $lockedLedger->booking;
            $booking->update([
                'payment_status' => PaymentStatusEnum::AUTHORIZED,
                'escrow_status' => EscrowStatusEnum::FUNDED,
                'booking_status' => BookingStatusEnum::CONFIRMED,
            ]);

            event(new BookingPaymentConfirmed($booking, $lockedLedger));

            return true;
        });
    }
}