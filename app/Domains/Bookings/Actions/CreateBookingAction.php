<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Assets\Exceptions\AssetNotAvailableException as AssetStatusException;
use App\Domains\Bookings\Exceptions\AssetNotAvailableException;
use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Domains\Shared\Enums\Booking\EscrowStatusEnum;
use App\Domains\Shared\Enums\Booking\HandoffMethodEnum;
use App\Domains\Shared\Enums\Booking\PaymentStatusEnum;
use App\Http\Requests\Bookings\CreateBookingRequest;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function execute(CreateBookingRequest $request, Asset $asset, User $renter): Booking
    {

        // GUARD 1: Asset status Check
        if ($asset->status !== StatusEnum::ACTIVE) {
            throw new AssetStatusException(
                'This asset is not currently available for booking.'
            );
        }

        // parse the validated datetime strings into Carbon objects to compare them, do math on them, and store them correctly.
        $startDatetime = Carbon::parse($request->validated('start_datetime'));
        $endDatetime = Carbon::parse($request->validated('end_datetime'));

        // GUARD 2: Availability check
        return DB::transaction(function () use ($asset, $renter, $request, $startDatetime, $endDatetime) {
            // lock a database row for preventing the race condition.
            // "while this transaction is open, no other transaction can modify the asset row."
            $asset = Asset::lockForUpdate()->findOrFail($asset->id);

            $conflictExists = Booking::where('asset_id', $asset->id)
                        // Why: cancelled bookings don't block the calendar
                ->where('booking_status', '!=', BookingStatusEnum::CANCELLED)
                ->where(function ($query) use ($startDatetime, $endDatetime) {
                    // The new start falls INSIDE an existing booking's window
                    $query->whereBetween('start_datetime', [$startDatetime, $endDatetime])
                            // The new end falls INSIDE an existing booking's window
                        ->orWhereBetween('end_datetime', [$startDatetime, $endDatetime])
                            // The new booking completely CONTAINS an existing booking
                        ->orWhere(function ($q) use ($startDatetime, $endDatetime) {
                            $q->where('start_datetime', '>=', $startDatetime)
                                ->where('end_datetime', '<=', $endDatetime);
                        });
                })->exists();
            if ($conflictExists) {
                throw new AssetNotAvailableException(
                    'This asset is already booked for the selected time period.'
                );
            }

            // ====================
            // PRICING CALCULATION
            // ====================

            $rentalHours = ceil($startDatetime->diffInHours($endDatetime));
            $rateApplied = $asset->hourly_rate;
            $totalRentalAmount = $rentalHours * $rateApplied;

            $securityDepositAmount = $asset->security_deposit;
            $platformFee = round($totalRentalAmount * 0.000, 2);
            $totalCharged = $totalRentalAmount + $platformFee + $securityDepositAmount;

            // CREATE THE BOOKING
            $booking = Booking::create([
                'asset_id' => $asset->id,
                'renter_id' => $renter->id,
                'owner_id' => $asset->owner_id,

                'start_datetime' => $startDatetime,
                'end_datetime' => $endDatetime,

                'rate_applied' => $rateApplied,
                'total_rental_amount' => $totalRentalAmount,
                'security_deposit_amount' => $securityDepositAmount,
                'platform_fee' => $platformFee,
                'total_charged' => $totalCharged,

                'booking_status' => BookingStatusEnum::PENDING,
                'payment_status' => PaymentStatusEnum::PENDING,
                'escrow_status' => EscrowStatusEnum::PENDING,

                'handoff_method' => HandoffMethodEnum::from($request->validated('handoff_method')),
            ]);

            return $booking;
        });
    }
}
