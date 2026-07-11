<?php

declare(strict_types=1);

namespace App\Livewire\Bookings;

use App\Domains\Assets\Exceptions\AssetNotAvailableException as AssetStatusException;
use App\Domains\Bookings\Actions\CreateBookingAction;
use App\Domains\Bookings\Exceptions\AssetNotAvailableException;
use App\Domains\Shared\Enums\Booking\HandoffMethodEnum;
use App\Http\Requests\Bookings\CreateBookingRequest;
use App\Models\Asset;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class BookingCreate extends Component
{
    // =====================
    // PROPERTIES
    // =====================

    public Asset $asset;

    // Why string not Carbon for dates?
    // Livewire properties must be serializable between requests.
    // Carbon objects are not directly serializable by Livewire.
    // We store the raw string from the datetime input and parse
    // it to Carbon only when we need to do math or pass to the action.
    public string $startDatetime = '';

    public string $endDatetime = '';

    // Why string not enum?
    // Same reason — enums aren't directly wire:model compatible.
    // We store the string value and convert to enum in the action.
    public string $handoffMethod = '';

    // Why these as properties and not computed?
    // They update reactively as the user changes dates.
    // Computed properties recalculate on every render — fine.
    // But we need to show a live pricing preview as dates change,
    // which means these values need to update instantly.
    public float $estimatedHours = 0;

    public float $estimatedRentalAmount = 0;

    public float $estimatedPlatformFee = 0;

    public float $estimatedTotal = 0;

    public string $errorMessage = '';

    // =====================
    // MOUNT
    // =====================

    public function mount(Asset $asset): void
    {
        // Why abort if not ACTIVE?
        // A paused or draft asset should not accept booking requests.
        // 404 not 403 — same reasoning as Show.php, reveals nothing.
        if ($asset->status->value !== 'ACTIVE') {
            abort(404);
        }

        // Why abort if owner?
        // An owner cannot book their own asset.
        // This duplicates the FormRequest guard but mount() catches
        // it at the page load level — no need to even render the form.
        if (auth()->id() === $asset->owner_id) {
            abort(403, 'You cannot book your own asset.');
        }

        $this->asset = $asset;

        // Why default handoff_method to first enum value?
        // The select dropdown needs an initial value. Without this,
        // the first option appears selected visually but
        // $handoffMethod is empty string — validation would fail
        // on submit even though the user "selected" something.
        $this->handoffMethod = HandoffMethodEnum::cases()[0]->value;
    }

    // =====================
    // REACTIVE PRICING
    // =====================

    // Why updatedStartDatetime and updatedEndDatetime?
    // Every time either date changes, recalculate the pricing preview.
    // The renter sees the cost update live as they pick dates —
    // no submit button needed to see the price.
    public function updatedStartDatetime(): void
    {
        $this->recalculatePricing();
    }

    public function updatedEndDatetime(): void
    {
        $this->recalculatePricing();
    }

    private function recalculatePricing(): void
    {
        // Why guard with empty check?
        // Both dates must exist before we can calculate.
        // If the user has only filled start but not end, skip.
        if (empty($this->startDatetime) || empty($this->endDatetime)) {
            return;
        }

        try {
            $start = Carbon::parse($this->startDatetime);
            $end = Carbon::parse($this->endDatetime);

            // Why return silently if end <= start?
            // The validation rule will catch this on submit.
            // Here we just don't want to show negative pricing.
            if ($end->lte($start)) {
                return;
            }

            // Why ceil()? Same reason as CreateBookingAction —
            // partial hours bill as full hours.
            $this->estimatedHours = ceil($start->diffInHours($end));
            $this->estimatedRentalAmount = $this->estimatedHours * $this->asset->hourly_rate;
            $this->estimatedPlatformFee = round($this->estimatedRentalAmount * 0.075, 2);

            // Why include deposit in estimated total?
            // The renter needs to know the full amount they'll be
            // charged — including the deposit held in escrow.
            $this->estimatedTotal = $this->estimatedRentalAmount
                + $this->estimatedPlatformFee
                + $this->asset->security_deposit;

        } catch (\Exception $e) {
            // Why swallow this exception?
            // Carbon::parse() throws if the string is not a valid
            // datetime. While the user is mid-typing, the string
            // is temporarily invalid. We don't want an error flash
            // every time they type a character. Silent return is
            // correct here — validation on submit handles real errors.
            return;
        }
    }

    // =====================
    // SUBMIT
    // =====================

    public function submit(CreateBookingAction $action): void
    {
        $this->errorMessage = '';

        // Why validate in the component and not a FormRequest?
        // This is a Livewire component calling an action directly —
        // there is no HTTP request object to attach a FormRequest to.
        // $this->validate() uses Laravel's validator under the hood,
        // same rules, same error messages, same @error() in the view.
        $this->validate([
            'startDatetime' => ['required', 'date', 'after:now'],
            'endDatetime' => ['required', 'date', 'after:startDatetime'],
            'handoffMethod' => [
                'required',
                'in:'.implode(',', HandoffMethodEnum::values()),
            ],
        ], [
            'startDatetime.required' => 'Please choose a start date and time.',
            'startDatetime.after' => 'The start time must be in the future.',
            'endDatetime.required' => 'Please choose an end date and time.',
            'endDatetime.after' => 'The end time must be after the start time.',
            'handoffMethod.required' => 'Please choose a handoff method.',
        ]);

        try {
            // Why build a fake request object?
            // CreateBookingAction expects a CreateBookingRequest.
            // In Livewire (Option B), there is no real HTTP request.
            // We create a minimal request object carrying the validated
            // data so the action interface stays unchanged — the action
            // doesn't need to know it's being called from Livewire.
            // This keeps your action reusable from both HTTP and Livewire.
            $fakeRequest = new CreateBookingRequest;
            $fakeRequest->setValidator(
                validator([
                    'asset_id' => $this->asset->id,
                    'start_datetime' => $this->startDatetime,
                    'end_datetime' => $this->endDatetime,
                    'handoff_method' => $this->handoffMethod,
                ], [
                    'asset_id' => 'required|string',
                    'start_datetime' => 'required|date',
                    'end_datetime' => 'required|date',
                    'handoff_method' => 'required|string',
                ])
            );

            $booking = $action->execute(
                $fakeRequest,
                $this->asset,
                auth()->user()
            );

            // Why redirect to BookingShow after creation?
            // The renter's next action is to see their booking
            // confirmation and wait for the owner to confirm.
            // BookingShow is exactly that page.
            $this->redirect(
                route('bookings.show', $booking->id),
                navigate: true
            );

        } catch (AssetStatusException $e) {
            $this->errorMessage = $e->getMessage();

        } catch (AssetNotAvailableException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    // =====================
    // RENDER
    // =====================

    public function render(): View
    {
        return view('livewire.bookings.create', [
            'handoffMethods' => HandoffMethodEnum::cases(),
        ])->layout('layouts.marketplace');
        // Why marketplace layout?
        // The booking creation page is entered from the public asset
        // detail page. Keeping the marketplace layout maintains
        // visual continuity — the renter doesn't feel like they've
        // jumped into a different application.
    }
}
