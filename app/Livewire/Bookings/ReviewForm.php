<?php

namespace App\Livewire\Bookings;

use App\Domains\Bookings\Actions\SubmitReviewAction;
use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ReviewForm extends Component
{
    // The booking this review belongs to — passed in via mount()
    public Booking $booking;

    // Form fields — these are reactive, meaning any change in the
    // Blade view is immediately reflected here without a page reload
    public int $rating = 0;
    public string $comment = '';

    // UI state flags
    public bool $submitted = false;
    public string $errorMessage = '';

    // Holds the review model after successful submission
    // so the view can switch from "show form" to "show review"
    public ?Review $submittedReview = null;

    public function mount(Booking $booking): void
    {
        $this->booking = $booking;

        // If a review already exists on this booking when the
        // component first loads, we pre-load it so the view
        // shows the submitted state immediately — not the blank form
        if ($booking->renter_review_submitted_at !== null) {
            $this->submittedReview = Review::where('booking_id', $booking->id)
                ->where('reviewer_id', Auth::id())
                ->first();

            $this->submitted = true;
        }
    }

    // Computed property — Livewire evaluates this on every render
    // It answers: "is the logged-in user the renter on this booking?"
    // The Blade view uses this to decide whether to show the form at all
    public function getIsRenterProperty(): bool
    {
        return Auth::id() === $this->booking->renter_id;
    }

    // Computed property — answers: "has this review already been submitted?"
    // Derived from the booking timestamp, not from a separate DB query
    public function getAlreadyReviewedProperty(): bool
    {
        return $this->booking->renter_review_submitted_at !== null;
    }

    public function setRating(int $rating): void
    {
        // Called by the star buttons in the Blade view via wire:click
        // Sets the reactive rating value — the stars re-render immediately
        $this->rating = $rating;
    }

    public function submitReview(): void
    {
        $this->errorMessage = '';

        $this->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'min:10', 'max:2000'],
        ]);

        try {
            $review = app(SubmitReviewAction::class)->execute(
                booking: $this->booking,
                rating: $this->rating,
                comment: $this->comment,
                reviewer: Auth::user(),
            );

            $this->submittedReview = $review;
            $this->booking->renter_review_submitted_at = \Illuminate\Support\Carbon::now();
            $this->submitted = true;
            $this->dispatch('reviewSubmitted');

        } catch (UnauthorizedBookingActionException $e) {
            $this->errorMessage = $e->getMessage();

        } catch (InvalidStateTransitionException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.bookings.review-form');
    }
}