<?php 
declare(static_types = 1);

namespace App\Http\Controllers\API\V1\Users;

use App\Domains\Bookings\Actions\SubmitReviewAction;
use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\InitiateReviewRequest;
use App\Http\Resources\Bookings\ReviewResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller{
    public function store( InitiateReviewRequest $request, Booking $booking, SubmitReviewAction $action): JsonResponse{
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