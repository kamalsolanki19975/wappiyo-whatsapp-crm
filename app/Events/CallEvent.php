<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class CallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $call;
    public string $eventType;
    public int $organizationId;
    public ?array $additionalData;

    /**
     * Create a new event instance.
     *
     * @param Call|array $call
     * @param string $eventType (e.g. 'initiated', 'ringing', 'connected', 'ended', 'missed', 'failed', 'incoming', 'notes_updated')
     * @param int $organizationId
     * @param array|null $additionalData
     */
    public function __construct($call, string $eventType, int $organizationId, ?array $additionalData = null)
    {
        $this->call = $call;
        $this->eventType = $eventType;
        $this->organizationId = $organizationId;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        try {
            // Check if Pusher or broadcast driver is configured
            $hasPusher = config('broadcasting.connections.pusher.key') && config('broadcasting.connections.pusher.secret');
            $driver = config('broadcasting.default');

            if ($hasPusher || in_array($driver, ['pusher', 'log', 'redis', 'null'], true)) {
                // Broadcast to calls channel and chats channel for real-time inbox update
                $channels[] = new Channel('calls.ch' . $this->organizationId);
                $channels[] = new Channel('chats.ch' . $this->organizationId);
            }
        } catch (Exception $e) {
            Log::error('Failed to configure broadcast channels for CallEvent: ' . $e->getMessage());
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'CallEvent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'call' => $this->call instanceof Call ? $this->call->loadMissing(['contact', 'agent']) : $this->call,
            'event_type' => $this->eventType,
            'organization_id' => $this->organizationId,
            'data' => $this->additionalData,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
