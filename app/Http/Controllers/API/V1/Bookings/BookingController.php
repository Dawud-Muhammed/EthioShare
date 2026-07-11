<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Bookings;

use App\Domains\Assets\Exceptions\AssetNotAvailableException as AssetStatusException;
use App\Domains\Bookings\Actions\CancelBookingAction;
use App\Domains\Bookings\Actions\CompleteBookingAction;
use App\Domains\Bookings\Actions\CompleteHandoffAction;
use App\Domains\Bookings\Actions\ConfirmBookingAction;
use App\Domains\Bookings\Actions\ConfirmRenterArrivalAction;
use App\Domains\Bookings\Actions\CreateBookingAction;
use App\Domains\Bookings\Actions\InitializeEscrowAction;
use App\Domains\Bookings\Exceptions\AssetNotAvailableException;
use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\AuthorizeEscrowRequest;
use App\Http\Requests\Bookings\CancelBookingRequest;
use App\Http\Requests\Bookings\CreateBookingRequest;
use App\Http\Resources\Bookings\BookingResource;
use App\Models\Asset;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(CreateBookingRequest $request, Asset $asset, CreateBookingAction $action): JsonResponse
    {
        try {
            $booking = $action->execute($request, $asset, auth()->user());

            return response()->json([
                'data' => new BookingResource($booking),
                'message' => 'Booking created successfully.',
            ], 201);
        } catch (AssetStatusException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (AssetNotAvailableException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    public function show(Booking $booking): JsonResponse
    {
        return response()->json([
            'data' => new BookingResource($booking),
        ]);
    }

    public function myBookings(Request $request): JsonResponse
    {
        $booking = Booking::where('renter_id', $request->user()->id)->latest()->paginate(10);

        return response()->json([
            'data' => BookingResource::collection($booking),
        ]);
    }

    public function myAssetBookings(Request $request): JsonResponse
    {
        $booking = Booking::where('owner_id', $request->user()->id)->latest()->paginate(10);

        return response()->json([
            'data' => BookingResource::collection($booking),
        ]);
    }

    public function Confirm(Request $request, Booking $booking, ConfirmBookingAction $action): JsonResponse
    {
        try {
            $booking = $action->execute($booking, $request->user());

            return response()->json([
                'data' => new BookingResource($booking),
                'message' => 'Booking confirmed successfully.',
            ]);
        } catch (UnauthorizedBookingActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (InvalidStateTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function Cancel(CancelBookingRequest $request, Booking $booking, CancelBookingAction $action): JsonResponse
    {
        try {
            $booking = $action->execute($booking, $request->user(), $request->validated('reason'));

            return response()->json([
                'data' => new BookingResource($booking),
                'message' => 'Booking canceled successfully.',
            ]);
        } catch (UnauthorizedBookingActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (InvalidStateTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function confirmArrival(Request $request, Booking $booking, ConfirmRenterArrivalAction $action): JsonResponse
    {
        try {
            $booking = $action->execute($booking, $request->user());

            return response()->json([
                'data' => new BookingResource($booking),
                'message' => 'Renter arrival confirmed successfully. Awaiting handoff.',
            ]);
        } catch (UnauthorizedBookingActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (InvalidStateTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function completeHandoff(Request $request, Booking $booking, CompleteHandoffAction $action): JsonResponse
    {
        try {
            $booking = $action->execute($booking, $request->user());

            return response()->json([
                'data' => new BookingResource($booking),
                'message' => 'Handoff completed successfully. Rental in progress.',
            ]);
        } catch (UnauthorizedBookingActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (InvalidStateTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function complete(Request $request, Booking $booking, CompleteBookingAction $action): JsonResponse
    {
        try {
            $booking = $action->execute($booking, $request->user());

            return response()->json([
                'data' => new BookingResource($booking),
                'message' => 'Booking completed successfully. Payout will be processed.',
            ]);
        } catch (UnauthorizedBookingActionException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (InvalidStateTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function authorizeEscrow(AuthorizeEscrowRequest $request, Booking $booking, InitializeEscrowAction $action): JsonResponse
    {
        $result = $action->execute($booking, $request->user());

        return response()->json([
            'data' => [
                'booking_id' => $booking->id,
                'checkout_url' => $result['checkout_url'],
            ],
            'meta' => [
                'message' => 'Redirect the renter to checkout_url to complete payment.',
            ],
        ], 201);
    }
}
