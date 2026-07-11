<?php

namespace App\Domains\Bookings\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EscrowInitializationException extends Exception
{
    private int $statusCode;

    private array $context;

    private function __construct(string $message, int $statusCode, array $context = [])
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->context = $context;
    }

    public static function alreadyInitialized(string $bookingId): self
    {
        return new self(
            'Escrow has already been initialized for this booking.',
            409,
            ['booking_id' => $bookingId]
        );
    }

    public static function invalidBookingStatus(string $bookingId, string $currentStatus): self
    {
        return new self(
            "Booking must be PENDING before escrow can be initialized. Current status: {$currentStatus}.",
            409,
            ['booking_id' => $bookingId, 'current_status' => $currentStatus]
        );
    }

    public static function gatewayDeclined(string $bookingId, string|array $gatewayMessage): self
    {
        $normalizedMessage = is_array($gatewayMessage)
            ? $gatewayMessage
            : ['message' => $gatewayMessage];

        return new self(
            'Payment authorization was declined by the gateway.',
            402,
            ['booking_id' => $bookingId, 'gateway_message' => $normalizedMessage, 'retry_possible' => true]
        );
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => $this->getMessage(),
            ...$this->context,
        ], $this->statusCode);
    }
}
