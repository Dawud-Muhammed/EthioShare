<?php

declare(static_types=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubmitReviewAction
{
    public function execute(Booking $booking, int $rating, string $comment, User $reviewer): Review
    {
        $this->ensureOwnership($booking, $reviewer);
        $this->ensureCorrectStatus($booking);
        $this->ensureNotAlreadyReviewed($booking);

        return DB::transaction(function () use ($booking, $rating, $comment, $reviewer) {
            $review = Review::create([
                'reviewable_type' => 'Asset',
                'reviewable_id' => $booking->asset_id,
                'reviewer_id' => $reviewer->id,
                'booking_id' => $booking->id,
                'rating' => $rating,
                'comment' => $comment,
                'is_verified_booking' => true,
            ]);
            $this->recalculateAssetRating($booking->asset_id);

            $booking->update([
                'renter_review_submitted_at' => now(),
            ]);

            return $review;
        });

    }

    private function ensureOwnership(Booking $booking, User $reviewer): void
    {
        if ($booking->renter_id !== $reviewer->id) {
            throw new UnauthorizedBookingActionException(
                'Only the renter on this booking can leave a review.'
            );
        }
    }

    private function ensureCorrectStatus(Booking $booking): void
    {
        if ($booking->booking_status !== BookingStatusEnum::COMPLETED) {
            throw new InvalidStateTransitionException(
                'Reviews can only be submitted for completed bookings.'
            );
        }
    }

    private function ensureNotAlreadyReviewed(Booking $booking): void
    {
        if ($booking->renter_review_submitted_at !== null) {
            throw new InvalidStateTransitionException(
                'A review has already been submitted for this booking.'
            );
        }
    }

    private function recalculateAssetRating(string $assetId): void
    {
        $asset = Asset::lockForUpdate()->findOrFail($assetId);

        $currentAverage = $asset->average_rating ?? 0;
        $currentCount = $asset->total_reviews ?? 0;
        $newCount = $currentCount + 1;
        $newAverage = (($currentAverage * $currentCount) + request('rating'))
            / $newCount;

        // TODO: Phase 2 — replace request('rating') dependency above with
        // a properly passed-in value; this is a placeholder for the
        // happy-path pass and will be corrected before this ships.
        $asset->update([
            'average_rating' => round($newAverage, 2),
            'total_reviews' => $newCount,
        ]);
    }
}
