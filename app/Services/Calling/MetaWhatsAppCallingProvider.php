<?php

namespace App\Services\Calling;

use App\Models\Call;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class MetaWhatsAppCallingProvider implements CallingProviderInterface
{
    private string $accessToken;
    private string $apiVersion;
    private ?string $appId;
    private ?string $phoneNumberId;
    private ?string $wabaId;
    private ?int $organizationId;
    private Client $httpClient;

    public function __construct(
        string $accessToken,
        string $apiVersion,
        ?string $appId,
        ?string $phoneNumberId,
        ?string $wabaId,
        ?int $organizationId = null,
        ?Client $httpClient = null
    ) {
        $this->accessToken = $accessToken;
        $this->apiVersion = $apiVersion ?: 'v20.0';
        $this->appId = $appId;
        $this->phoneNumberId = $phoneNumberId;
        $this->wabaId = $wabaId;
        $this->organizationId = $organizationId;
        $this->httpClient = $httpClient ?: new Client([
            'timeout' => 15,
            'connect_timeout' => 10,
        ]);
    }

    /**
     * Check whether the business has permission to call the customer.
     */
    public function checkCallPermission(string $customerPhone): array
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            return [
                'can_call' => false,
                'reason' => 'WhatsApp credentials (phone_number_id or access_token) not configured.',
                'action_required' => 'configure_credentials',
                'raw' => [],
            ];
        }

        $waId = $this->cleanWaId($customerPhone);
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/call_permissions";

        try {
            $response = $this->httpClient->get($url, [
                'headers' => $this->getHeaders(),
                'query' => [
                    'user_wa_id' => $waId,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true) ?: [];

            // Meta response formats:
            // {"data": [{"can_call": true, "user_wa_id": "...", "actions": ["start_call"]}]}
            // or {"can_call": true, ...}
            $data = $body['data'][0] ?? $body;
            $canCall = (bool) ($data['can_call'] ?? false);
            $actions = $data['actions'] ?? [];

            if (!$canCall && in_array('start_call', $actions, true)) {
                $canCall = true;
            }

            return [
                'can_call' => $canCall,
                'reason' => $data['reason'] ?? ($canCall ? null : 'User call permission request required.'),
                'action_required' => $canCall ? null : ($data['action_required'] ?? 'send_call_permission_request'),
                'raw' => $body,
            ];
        } catch (RequestException $e) {
            $errorMessage = $this->sanitizeErrorMessage($e);
            Log::warning("Meta WhatsApp Calling checkCallPermission error for org {$this->organizationId}: {$errorMessage}");

            return [
                'can_call' => false,
                'reason' => $errorMessage,
                'action_required' => 'verify_eligibility',
                'raw' => [],
            ];
        } catch (\Throwable $e) {
            Log::error("Unexpected error in checkCallPermission: " . $e->getMessage());

            return [
                'can_call' => false,
                'reason' => 'Unable to verify WhatsApp call permission with Meta.',
                'action_required' => null,
                'raw' => [],
            ];
        }
    }

    /**
     * Initiate an outbound call to a customer.
     */
    public function initiateCall(Call $call, array $options = []): array
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            return [
                'success' => false,
                'provider_call_id' => null,
                'status' => CallStatusProcessor::STATUS_FAILED,
                'error' => 'WhatsApp Cloud API phone number ID or Access Token is missing.',
                'raw' => [],
            ];
        }

        $waId = $this->cleanWaId($call->customer_phone);
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/calls";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $waId,
            'action' => 'connect',
        ];

        // If client provided WebRTC session/SDP offer
        if (!empty($options['session'])) {
            $payload['session'] = $options['session'];
        } elseif (!empty($options['sdp'])) {
            $payload['session'] = [
                'sdp_type' => 'offer',
                'sdp' => $options['sdp'],
            ];
        }

        try {
            $response = $this->httpClient->post($url, [
                'headers' => $this->getHeaders(),
                'json' => $payload,
            ]);

            $body = json_decode((string) $response->getBody(), true) ?: [];

            // Meta returns call_id or id in response
            $providerCallId = $body['call_id'] ?? $body['id'] ?? $body['calls'][0]['id'] ?? null;
            $status = CallStatusProcessor::map($body['status'] ?? 'initiating', 'connect');

            return [
                'success' => true,
                'provider_call_id' => $providerCallId,
                'status' => $status,
                'raw' => $body,
                'error' => null,
            ];
        } catch (RequestException $e) {
            $error = $this->sanitizeErrorMessage($e);
            Log::error("Meta WhatsApp Calling initiateCall failed for org {$this->organizationId}: {$error}");

            return [
                'success' => false,
                'provider_call_id' => null,
                'status' => CallStatusProcessor::STATUS_FAILED,
                'error' => $error,
                'raw' => [],
            ];
        } catch (\Throwable $e) {
            Log::error("Unexpected exception in initiateCall: " . $e->getMessage());

            return [
                'success' => false,
                'provider_call_id' => null,
                'status' => CallStatusProcessor::STATUS_FAILED,
                'error' => 'Unexpected error communicating with WhatsApp Calling API.',
                'raw' => [],
            ];
        }
    }

    /**
     * Accept/Answer an incoming call.
     */
    public function acceptCall(Call $call, array $options = []): array
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            return [
                'success' => false,
                'status' => $call->status,
                'error' => 'WhatsApp Cloud API credentials not configured.',
                'raw' => [],
            ];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/calls";
        $action = !empty($options['pre_accept']) ? 'pre_accept' : 'accept';

        $payload = [
            'messaging_product' => 'whatsapp',
            'call_id' => $call->provider_call_id,
            'action' => $action,
        ];

        if (!empty($options['session'])) {
            $payload['session'] = $options['session'];
        }

        try {
            $response = $this->httpClient->post($url, [
                'headers' => $this->getHeaders(),
                'json' => $payload,
            ]);

            $body = json_decode((string) $response->getBody(), true) ?: [];

            return [
                'success' => true,
                'status' => CallStatusProcessor::STATUS_CONNECTED,
                'raw' => $body,
                'error' => null,
            ];
        } catch (RequestException $e) {
            $error = $this->sanitizeErrorMessage($e);
            Log::error("Meta WhatsApp Calling acceptCall failed: {$error}");

            return [
                'success' => false,
                'status' => $call->status,
                'error' => $error,
                'raw' => [],
            ];
        }
    }

    /**
     * Terminate an active call.
     */
    public function terminateCall(Call $call, array $options = []): array
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            return [
                'success' => false,
                'status' => CallStatusProcessor::STATUS_COMPLETED,
                'error' => 'WhatsApp Cloud API credentials not configured.',
                'raw' => [],
            ];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/calls";

        $payload = [
            'messaging_product' => 'whatsapp',
            'action' => 'terminate',
        ];

        if (!empty($call->provider_call_id)) {
            $payload['call_id'] = $call->provider_call_id;
        }

        try {
            $response = $this->httpClient->post($url, [
                'headers' => $this->getHeaders(),
                'json' => $payload,
            ]);

            $body = json_decode((string) $response->getBody(), true) ?: [];

            return [
                'success' => true,
                'status' => CallStatusProcessor::STATUS_COMPLETED,
                'raw' => $body,
                'error' => null,
            ];
        } catch (RequestException $e) {
            $error = $this->sanitizeErrorMessage($e);
            Log::warning("Meta WhatsApp Calling terminateCall warning: {$error}");

            // Still mark completed locally if Meta already terminated it
            return [
                'success' => true,
                'status' => CallStatusProcessor::STATUS_COMPLETED,
                'error' => $error,
                'raw' => [],
            ];
        }
    }

    /**
     * Query phone number calling capabilities and status from Meta.
     */
    public function getCallingStatus(): array
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            return [
                'enabled' => false,
                'status' => 'not_configured',
                'raw' => [],
            ];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}?fields=display_phone_number,verified_name,quality_rating,messaging_limit_tier";

        try {
            $response = $this->httpClient->get($url, [
                'headers' => $this->getHeaders(),
            ]);

            $body = json_decode((string) $response->getBody(), true) ?: [];

            return [
                'enabled' => true,
                'status' => 'connected',
                'display_phone_number' => $body['display_phone_number'] ?? null,
                'verified_name' => $body['verified_name'] ?? null,
                'quality_rating' => $body['quality_rating'] ?? 'UNKNOWN',
                'messaging_limit_tier' => $body['messaging_limit_tier'] ?? 'TIER_1K',
                'raw' => $body,
            ];
        } catch (RequestException $e) {
            $error = $this->sanitizeErrorMessage($e);
            Log::warning("Meta WhatsApp Calling getCallingStatus error: {$error}");

            return [
                'enabled' => false,
                'status' => 'error',
                'error' => $error,
                'raw' => [],
            ];
        }
    }

    public function mapProviderStatusToInternal(string $providerStatus, ?string $event = null): string
    {
        return CallStatusProcessor::map($providerStatus, $event);
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    private function cleanWaId(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Sanitize error message to prevent leaking tokens or internal secrets.
     */
    private function sanitizeErrorMessage(RequestException $e): string
    {
        $response = $e->getResponse();
        if ($response) {
            $body = json_decode((string) $response->getBody(), true);
            if (!empty($body['error']['message'])) {
                $msg = $body['error']['message'];
                // Mask any bearer tokens or keys if accidentally reflected in message
                $msg = preg_replace('/Bearer\s+[A-Za-z0-9\-\._~+\/]+=*/i', 'Bearer [MASKED]', $msg);
                return $msg;
            }
        }

        return 'WhatsApp Calling request failed. Please check phone number configuration and permissions.';
    }
}
