<?php

namespace App\Livewire\Bookings;

use App\Domains\Bookings\Actions\CancelBookingAction;
use App\Domains\Bookings\Actions\CompleteBookingAction;
use App\Domains\Bookings\Actions\CompleteHandoffAction;
use App\Domains\Bookings\Actions\ConfirmBookingAction;
use App\Domains\Bookings\Actions\ConfirmRenterArrivalAction;
use App\Domains\Bookings\Actions\InitializeEscrowAction;
use App\Domains\Bookings\Exceptions\EscrowInitializationException;
use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Domains\Shared\Enums\Booking\EscrowStatusEnum;
use App\Models\Booking;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BookingShow extends Component
{
    // =====================
    // PROPERTIES
    // =====================
    public Booking $booking;

    public string $successMessage = '';

    public string $errorMessage = '';

    public bool $showCancelConfirm = false;

    public string $cancelReason = '';

    // =====================
    // MOUNT
    // =====================
    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load([
            'asset',
            'owner',
            'renter',
            'handoffLocation',
        ]);
    }

    // =====================
    // COMPUTED PROPERTIES
    // =====================

    #[Computed]
    public function isRenter(): bool
    {
        return auth()->id() === $this->booking->renter_id;
    }

    #[Computed]
    public function isOwner(): bool
    {
        return auth()->id() === $this->booking->owner_id;
    }

    // Why PENDING not CONFIRMED?
    // ConfirmBookingAction moves PENDING → CONFIRMED.
    // The button must show BEFORE the transition, when status is PENDING.
    // Checking CONFIRMED means the button shows after it's already confirmed — wrong.
    #[Computed]
    public function canConfirmBooking(): bool
    {
        return $this->isOwner
            && $this->booking->booking_status === BookingStatusEnum::PENDING;
    }

    #[Computed]
    public function canCancel(): bool
    {
        $cancellableStatuses = [
            BookingStatusEnum::PENDING,
            BookingStatusEnum::CONFIRMED,
        ];

        // Why $this->isRenter not $this->isRenter()?
        // isRenter is a computed property, not a plain method.
        // Computed properties are accessed as properties — no parentheses.
        // Using () bypasses Livewire's cache and calls the method directly.
        return ($this->isRenter || $this->isOwner)
            && in_array($this->booking->booking_status, $cancellableStatuses, true);
    }

    #[Computed]
    public function canArrive(): bool
    {
        return $this->isRenter
            && $this->booking->booking_status === BookingStatusEnum::CONFIRMED;
    }

    #[Computed]
    public function canCompleteHandoff(): bool
    {
        return $this->isOwner
            && $this->booking->booking_status === BookingStatusEnum::RENTER_ARRIVED;
    }

    #[Computed]
    public function canComplete(): bool
    {
        return $this->isOwner
            && $this->booking->booking_status === BookingStatusEnum::IN_PROGRESS;
    }

    // =====================
    // ACTION METHODS
    // =====================

    public function confirmBooking(ConfirmBookingAction $action): void
    {
        $this->clearMessages();

        try {
            $this->booking = $action->execute($this->booking, auth()->user());
            $this->successMessage = 'Booking confirmed successfully.';

        } catch (UnauthorizedBookingActionException $e) {
            $this->errorMessage = $e->getMessage();

        } catch (InvalidStateTransitionException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function cancelBooking(CancelBookingAction $action): void
    {
        $this->clearMessages();

        $this->validate([
            'cancelReason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        try {
            $this->booking = $action->execute(
                $this->booking,
                auth()->user(),
                $this->cancelReason
            );

            $this->showCancelConfirm = false;
            $this->cancelReason = '';
            $this->successMessage = 'Booking cancelled.';

        } catch (UnauthorizedBookingActionException $e) {
            $this->errorMessage = $e->getMessage();

        } catch (InvalidStateTransitionException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function confirmArrival(ConfirmRenterArrivalAction $action): void
    {
        $this->clearMessages();

        try {
            $this->booking = $action->execute($this->booking, auth()->user());
            $this->successMessage = 'Arrival confirmed. The owner has been notified.';

        } catch (UnauthorizedBookingActionException $e) {
            $this->errorMessage = $e->getMessage();

        } catch (InvalidStateTransitionException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function completeHandoff(CompleteHandoffAction $action): void
    {
        $this->clearMessages();

        try {
            $this->booking = $action->execute($this->booking, auth()->user());
            $this->successMessage = 'Handoff complete. Rental is now in progress.';

        } catch (UnauthorizedBookingActionException $e) {
            $this->errorMessage = $e->getMessage();

        } catch (InvalidStateTransitionException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function completeBooking(CompleteBookingAction $action): void
    {
        $this->clearMessages();

        try {
            $this->booking = $action->execute($this->booking, auth()->user());
            $this->successMessage = 'Rental completed. Payout will be processed shortly.';

        } catch (UnauthorizedBookingActionException $e) {
            $this->errorMessage = $e->getMessage();

        } catch (InvalidStateTransitionException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function payNow(InitializeEscrowAction $action){
    $this->errorMessage = '';

    try {
        $result = $action->execute($this->booking, auth()->user());
    } catch (EscrowInitializationException $e) {
        $this->errorMessage = $e->getMessage();
        return;
    }

    return $this->redirect($result['checkout_url']);
   }

    #[Computed]
    public function canPayNow(): bool
    {
        return $this->booking->renter_id === auth()->id()
            && $this->booking->booking_status === BookingStatusEnum::PENDING
            && $this->booking->escrow_status === EscrowStatusEnum::PENDING;
    }
    // =====================
    // HELPERS
    // =====================

    private function clearMessages(): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    // =====================
    // RENDER
    // =====================

    public function render()
    {
        return view('livewire.bookings.show')
            ->layout('layouts.app');
    }
}
