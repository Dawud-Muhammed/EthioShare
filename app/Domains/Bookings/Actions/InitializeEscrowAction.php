<?php

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Exceptions\EscrowInitializationException;
use App\Domains\Bookings\Services\ChapaGatewayService;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Domains\Shared\Enums\Booking\EscrowStatusEnum;
use App\Domains\Shared\Enums\EscrowLedger\EscrowLedgerStatus;
use App\Models\Booking;
use App\Models\EscrowLedger;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InitializeEscrowAction
{
    public function __construct(
        private readonly ChapaGatewayService $gateway,
    ) {}

    public function execute(Booking $booking, User $initiator): array
    {
        // Ownership doesn't need a database lock -- identity isn't
        // going to change between now and the transaction below -- so
        // we check it first and fail fast, before opening anything.
        $this->ensureOwnership($booking, $initiator);

        return DB::transaction(function () use ($booking) {
            // Re-fetch the booking WITH A LOCK, inside the transaction,
            // even though we already have a copy of it above. Here's
            // why: the $booking we were handed could be milliseconds
            // stale. If the renter double-clicks "Pay", two requests
            // can both read booking_status = PENDING at nearly the same
            // instant, both pass the guard check below, and both go on
            // to create a ledger row and charge Chapa. This is called a
            // TOCTOU bug -- time-of-check to time-of-use -- and it's
            // exactly the same class of race condition your
            // CreateBookingAction already guards against for double-
            // booking. lockForUpdate() tells Postgres: nobody else
            // reads or writes this row until my transaction finishes.
            // The second click has to wait right here, and by the time
            // it's allowed through, the first click will have already
            // changed escrow_status -- so the second click's guard
            // check below correctly fails instead of silently
            // duplicating the charge.
            $booking = Booking::lockForUpdate()->findOrFail($booking->id);

            $this->ensureCorrectBookingStatus($booking);
            $this->ensureEscrowNotAlreadyInitialized($booking);

            $txRef = $this->generateTransactionReference($booking);

            // Created INSIDE the transaction, not before it. If
            // gateway->authorize() below throws, Laravel automatically
            // rolls back everything that happened in this closure --
            // including this row. That's exactly what we want: a
            // ledger row that claims "payment pending" should never
            // exist for a payment that never got a real checkout link
            // from Chapa.
            $ledger = EscrowLedger::create([
                'booking_id' => $booking->id,
                'transaction_id' => $txRef,
                'gateway_name' => 'CHAPA',
                'amount_due' => $booking->total_charged,
                'status' => EscrowLedgerStatus::PENDING_PAYMENT, // adjust the case name if yours differs
                'transactions' => [],
            ]);

            $result = $this->gateway->authorize([
                'amount' => $booking->total_charged,
                'email' => $booking->renter->email,
                'first_name' => $booking->renter->first_name,
                'last_name' => $booking->renter->last_name,
                'phone_number' => $booking->renter->phone_number,
                'tx_ref' => $txRef,
            ]);

            return [
                'escrow_ledger' => $ledger,
                'checkout_url' => $result['checkout_url'],
            ];
        });
    }

    private function ensureOwnership(Booking $booking, User $initiator): void
    {
        if ($booking->renter_id !== $initiator->id) {
            // You already built UnauthorizedBookingActionException for
            // exactly this case -- reuse it rather than inventing a new
            // one here. I haven't seen that file's constructor, so
            // match whatever shape you already gave it; if it just
            // takes a message, this is enough:
            throw new \App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException(
                'You are not authorized to initialize payment for this booking.'
            );
        }
    }

    private function ensureCorrectBookingStatus(Booking $booking): void
    {
        if ($booking->booking_status !== BookingStatusEnum::PENDING) {
            throw EscrowInitializationException::invalidBookingStatus(
                $booking->id,
                $booking->booking_status->value
            );
        }
    }

    private function ensureEscrowNotAlreadyInitialized(Booking $booking): void
    {
        if ($booking->escrow_status !== EscrowStatusEnum::PENDING) {
            throw EscrowInitializationException::alreadyInitialized($booking->id);
        }
    }

    private function generateTransactionReference(Booking $booking): string
    {
        // Chapa requires a UNIQUE tx_ref per attempt, not per booking.
        // If a renter's card is declined and they retry, that retry
        // needs its own reference -- reusing one gets rejected by
        // Chapa as a duplicate transaction. Booking ID plus a short
        // random suffix guarantees uniqueness without a separate
        // counter column to maintain.
        return "baza-{$booking->id}-" . Str::random(8);
    }
}