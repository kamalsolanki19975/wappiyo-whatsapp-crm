<?php

namespace App\Services\Calling;

class CallStatusProcessor
{
    public const STATUS_INITIATING = 'initiating';
    public const STATUS_RINGING    = 'ringing';
    public const STATUS_CONNECTING = 'connecting';
    public const STATUS_CONNECTED  = 'connected';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_MISSED     = 'missed';
    public const STATUS_REJECTED   = 'rejected';
    public const STATUS_BUSY       = 'busy';
    public const STATUS_FAILED     = 'failed';
    public const STATUS_CANCELLED  = 'cancelled';
    public const STATUS_UNKNOWN    = 'unknown';

    /**
     * Map provider status/event to internal normalized status.
     *
     * @param string|null $providerStatus
     * @param string|null $event
     * @return string
     */
    public static function map(?string $providerStatus, ?string $event = null): string
    {
        $statusKey = strtolower(trim((string) $providerStatus));
        $eventKey = strtolower(trim((string) $event));

        // Prioritize explicit event if provided
        if (!empty($eventKey)) {
            switch ($eventKey) {
                case 'ringing':
                    return self::STATUS_RINGING;
                case 'pre_accept':
                case 'connecting':
                    return self::STATUS_CONNECTING;
                case 'accept':
                case 'accepted':
                case 'connected':
                case 'in_progress':
                    return self::STATUS_CONNECTED;
                case 'terminate':
                case 'terminated':
                case 'completed':
                case 'ended':
                    return self::STATUS_COMPLETED;
                case 'missed':
                case 'no_answer':
                case 'timeout':
                    return self::STATUS_MISSED;
                case 'rejected':
                case 'declined':
                    return self::STATUS_REJECTED;
                case 'busy':
                    return self::STATUS_BUSY;
                case 'failed':
                case 'error':
                    return self::STATUS_FAILED;
                case 'cancelled':
                case 'canceled':
                    return self::STATUS_CANCELLED;
            }
        }

        switch ($statusKey) {
            case 'initiating':
            case 'queued':
                return self::STATUS_INITIATING;
            case 'ringing':
                return self::STATUS_RINGING;
            case 'connecting':
            case 'pre_accept':
                return self::STATUS_CONNECTING;
            case 'connected':
            case 'accepted':
            case 'in_progress':
                return self::STATUS_CONNECTED;
            case 'completed':
            case 'terminated':
            case 'ended':
                return self::STATUS_COMPLETED;
            case 'missed':
            case 'no_answer':
                return self::STATUS_MISSED;
            case 'rejected':
            case 'declined':
                return self::STATUS_REJECTED;
            case 'busy':
                return self::STATUS_BUSY;
            case 'failed':
            case 'error':
                return self::STATUS_FAILED;
            case 'cancelled':
            case 'canceled':
                return self::STATUS_CANCELLED;
            default:
                return self::STATUS_UNKNOWN;
        }
    }

    /**
     * Check if status is a terminal (final) status.
     *
     * @param string $status
     * @return bool
     */
    public static function isFinal(string $status): bool
    {
        return in_array($status, [
            self::STATUS_COMPLETED,
            self::STATUS_MISSED,
            self::STATUS_REJECTED,
            self::STATUS_BUSY,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ], true);
    }

    /**
     * Determine if a transition from currentStatus to newStatus is permissible.
     * Prevents out-of-order events from reverting final calls.
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    public static function canTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true;
        }

        // Once completed or in terminal state, do not revert to active/in-progress states
        if (self::isFinal($currentStatus)) {
            return false;
        }

        return true;
    }

    /**
     * Helper alias for mapping Meta call events.
     *
     * @param string $event
     * @return string
     */
    public static function mapMetaEventToStatus(string $event): string
    {
        return self::map(null, $event);
    }

    /**
     * Helper alias for validating transitions.
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    public static function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        return self::canTransition($currentStatus, $newStatus);
    }
}
