<?php

namespace App\Http\Controllers\API\V1\Users;

use App\Domains\Bookings\Actions\SubmitReviewAction;
use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\InitiateReviewRequest;
use App\Http\Resources\Bookings\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        // "given" tab — reviews this user wrote as a renter
        // "received" tab — reviews left on assets this user owns
        // Default to "given" if no tab is specified
        $tab = $request->query('tab', 'given');

        if ($tab === 'received') {
            // Reviews where the reviewable is an Asset that belongs
            // to the authenticated user. We use whereHas to scope
            // only to this user's assets without loading all assets
            // into memory first.
            $reviews = Review::with(['reviewer', 'reviewable'])
                ->where('reviewable_type', 'Asset')
                ->whereHas('reviewable', function ($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })
                ->latest()
                ->paginate(10);
        } else {
            // Reviews written by the authenticated user
            $reviews = Review::with(['reviewable'])
                ->where('reviewer_id', $user->id)
                ->latest()
                ->paginate(10);
        }

        return ReviewResource::collection($reviews);
    }

    public function store(
        InitiateReviewRequest $request,
        Booking $booking,
        SubmitReviewAction $action
    ): JsonResponse {
        try {
            $review = $action->execute(
                booking: $booking,
                rating: $request->validated('rating'),
                comment: $request->validated('comment'),
                reviewer: $request->user(),
            );
        } catch (UnauthorizedBookingActionException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 403);
        } catch (InvalidStateTransitionException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }

        return (new ReviewResource($review))
            ->response()
            ->setStatusCode(201);
    }
}
