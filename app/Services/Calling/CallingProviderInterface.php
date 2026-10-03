<?php

namespace App\Services\Calling;

use App\Models\Call;

interface CallingProviderInterface
{
    /**
     * Check whether the business has permission to call the customer.
     * Meta API: GET /{version}/{phone_number_id}/call_permissions?user_wa_id={wa_id}
     *
     * @param string $customerPhone
     * @return array ['can_call' => bool, 'reason' => ?string, 'action_required' => ?string, 'raw' => array]
     */
    public function checkCallPermission(string $customerPhone): array;

    /**
     * Initiate an outbound call to a customer.
     * Meta API: POST /{version}/{phone_number_id}/calls with action: "connect"
     *
     * @param Call $call
     * @param array $options
     * @return array ['success' => bool, 'provider_call_id' => ?string, 'status' => string, 'raw' => array, 'error' => ?string]
     */
    public function initiateCall(Call $call, array $options = []): array;

    /**
     * Accept/Answer an incoming call.
     * Meta API: POST /{version}/{phone_number_id}/calls with action: "accept" / "pre_accept"
     *
     * @param Call $call
     * @param array $options
     * @return array ['success' => bool, 'status' => string, 'raw' => array, 'error' => ?string]
     */
    public function acceptCall(Call $call, array $options = []): array;

    /**
     * Terminate an active call.
     * Meta API: POST /{version}/{phone_number_id}/calls with action: "terminate"
     *
     * @param Call $call
     * @param array $options
     * @return array ['success' => bool, 'status' => string, 'raw' => array, 'error' => ?string]
     */
    public function terminateCall(Call $call, array $options = []): array;

    /**
     * Query phone number calling capabilities and status from Meta.
     *
     * @return array ['enabled' => bool, 'status' => string, 'raw' => array]
     */
    public function getCallingStatus(): array;

    /**
     * Map provider status/event to internal normalized status.
     *
     * @param string $providerStatus
     * @param string|null $event
     * @return string
     */
    public function mapProviderStatusToInternal(string $providerStatus, ?string $event = null): string;
}
