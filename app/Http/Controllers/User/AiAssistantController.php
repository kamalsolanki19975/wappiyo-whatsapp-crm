<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Addon;
use App\Models\Organization;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AiAssistantController extends BaseController
{
    /**
     * Display the AI Assistant screen.
     */
    public function index(Request $request)
    {
        $organizationId = session()->get('current_organization');
        if (!$organizationId) {
            abort(403, 'No active organization session.');
        }

        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $historyKey = 'ai_chat_history_org_' . $organizationId;
        $history = session()->get($historyKey, []);

        // Sample preset prompts tailored for Wappiyo WhatsApp CRM
        $presetPrompts = [
            [
                'id' => 'broadcast_promo',
                'category' => 'Campaigns',
                'title' => __('Promotional Broadcast Copy'),
                'prompt' => __('Draft a high-converting WhatsApp promotional template for our weekend sale with a limited-time 20% discount and a clear call-to-action button.')
            ],
            [
                'id' => 'support_escalation',
                'category' => 'Customer Support',
                'title' => __('Polite Escalation Response'),
                'prompt' => __('Compose an empathetic WhatsApp reply for a customer whose order delivery is delayed, assuring them our support team is actively tracking it.')
            ],
            [
                'id' => 'order_confirmation',
                'category' => 'Templates',
                'title' => __('Meta-Compliant Order Template'),
                'prompt' => __('Generate a Meta Cloud API approved utility template for order confirmation including placeholders {{1}} for customer name and {{2}} for order number.')
            ],
            [
                'id' => 'cart_recovery',
                'category' => 'Automation',
                'title' => __('Abandoned Cart Follow-Up'),
                'prompt' => __('Write an automated WhatsApp follow-up message to recover an abandoned checkout without sounding overly aggressive.')
            ],
        ];

        return Inertia::render('User/Ai/Index', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'presetPrompts' => $presetPrompts,
            'initialHistory' => $history,
            'aimodule' => true,
        ]);
    }

    /**
     * Send a prompt to the AI Assistant and receive an intelligent response.
     */
    public function chat(Request $request)
    {
        $organizationId = session()->get('current_organization');
        if (!$organizationId) {
            return response()->json([
                'success' => false,
                'message' => __('Unauthorized session.')
            ], 403);
        }

        $organization = Organization::where('id', $organizationId)->firstOrFail();

        $rawPrompt = $request->input('prompt') ?: $request->input('message');
        if (empty($rawPrompt) || strlen(trim((string) $rawPrompt)) < 2) {
            return response()->json([
                'success' => false,
                'message' => __('Prompt cannot be empty and must be at least 2 characters.')
            ], 422);
        }

        $prompt = strip_tags(trim((string) $rawPrompt));

        if (empty($prompt)) {
            return response()->json([
                'success' => false,
                'message' => __('Prompt cannot be empty.')
            ], 422);
        }

        $historyKey = 'ai_chat_history_org_' . $organizationId;
        $history = session()->get($historyKey, []);

        // Call OpenAI or intelligent fallback
        $aiResponse = $this->generateAiResponse($prompt, $organization);

        $userMessage = [
            'id' => (string) Str::uuid(),
            'sender' => 'user',
            'text' => $prompt,
            'time' => now()->format('h:i A'),
        ];

        $assistantMessage = [
            'id' => (string) Str::uuid(),
            'sender' => 'assistant',
            'text' => $aiResponse,
            'time' => now()->format('h:i A'),
        ];

        $history[] = $userMessage;
        $history[] = $assistantMessage;

        // Keep last 30 messages in session to avoid bloat
        if (count($history) > 30) {
            $history = array_slice($history, -30);
        }

        session()->put($historyKey, $history);

        return response()->json([
            'success' => true,
            'reply' => $aiResponse,
            'user_message' => $userMessage,
            'assistant_message' => $assistantMessage,
            'history' => $history,
        ]);
    }

    /**
     * Clear the conversation history for the current tenant.
     */
    public function clearHistory(Request $request)
    {
        $organizationId = session()->get('current_organization');
        if ($organizationId) {
            session()->forget('ai_chat_history_org_' . $organizationId);
        }

        return response()->json([
            'success' => true,
            'message' => __('Conversation history cleared.')
        ]);
    }

    /**
     * Generate response via OpenAI client or intelligent contextual fallback.
     */
    private function generateAiResponse(string $prompt, Organization $organization): string
    {
        $apiKey = config('services.openai.api_key') 
            ?: env('OPENAI_API_KEY') 
            ?: Setting::where('key', 'openai_api_key')->value('value');

        if (!empty($apiKey) && class_exists('\OpenAI')) {
            try {
                $client = \OpenAI::client($apiKey);
                $systemPrompt = "You are Wappiyo AI, a specialized WhatsApp CRM and marketing assistant for the organization '{$organization->name}'. Help users draft WhatsApp templates, reply to customer inquiries, create broadcast campaigns, and optimize conversational customer service. Format responses clearly with markdown bullet points and emojis where helpful.";

                $result = $client->chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 600,
                    'temperature' => 0.7,
                ]);

                if (!empty($result->choices[0]->message->content)) {
                    return trim($result->choices[0]->message->content);
                }
            } catch (\Throwable $e) {
                Log::warning('OpenAI API request failed: ' . $e->getMessage());
                // Fall through to contextual CRM generator
            }
        }

        // Intelligent contextual responses for Wappiyo WhatsApp CRM operations
        return $this->contextualWappiyoResponse($prompt, $organization);
    }

    /**
     * Contextual intelligent fallback response generator when OpenAI key is offline or unconfigured.
     */
    private function contextualWappiyoResponse(string $prompt, Organization $organization): string
    {
        $lower = strtolower($prompt);

        if (str_contains($lower, 'promot') || str_contains($lower, 'sale') || str_contains($lower, 'broadcast') || str_contains($lower, 'discount')) {
            return "🎉 **Exclusive Weekend Offer from {$organization->name}!**\n\n"
                . "Hello {{1}}! We have a special treat for you this weekend.\n\n"
                . "Enjoy **20% OFF** your next booking or order when you use code **SAVE20** at checkout.\n\n"
                . "⚡ *Offer valid until Sunday midnight only!*\n\n"
                . "👉 **Quick Reply Buttons:**\n"
                . "• [Claim 20% Discount]\n"
                . "• [Speak to an Agent]\n\n"
                . "*Tip: Remember to submit this template in the Message Templates tab under the MARKETING category before broadcasting.*";
        }

        if (str_contains($lower, 'delay') || str_contains($lower, 'support') || str_contains($lower, 'escalat') || str_contains($lower, 'problem')) {
            return "💬 **Suggested Customer Support Response:**\n\n"
                . "\"Hi {{1}}, thank you for reaching out to {$organization->name}.\n\n"
                . "We sincerely apologize for the delay with your order {{2}}. Our operations team is actively looking into the tracking details right now to get this expedited for you.\n\n"
                . "I will personally follow up with an update within the next 30 minutes. We truly appreciate your patience!\"\n\n"
                . "📋 *Recommended Agent Action: Add internal note on ticket #{{3}} and set priority to High.*";
        }

        if (str_contains($lower, 'cart') || str_contains($lower, 'abandon') || str_contains($lower, 'checkout')) {
            return "🛒 **Abandoned Cart Follow-Up Template:**\n\n"
                . "\"Hi {{1}}! We noticed you left some great items in your cart at {$organization->name}.\n\n"
                . "Items are selling out fast, but we've saved your bag so you don't miss out! Would you like us to help complete your order?\"\n\n"
                . "👉 **Buttons:**\n"
                . "• [Complete My Order]\n"
                . "• [Ask a Question]\n\n"
                . "*Meta Tip: Send this within 1 to 2 hours of cart abandonment for optimal 35%+ recovery rates.*";
        }

        if (str_contains($lower, 'order') || str_contains($lower, 'confirm') || str_contains($lower, 'receipt')) {
            return "✅ **Meta-Compliant Order Confirmation Template (UTILITY):**\n\n"
                . "*Header:* Order Confirmed #{{2}}\n\n"
                . "*Body:*\n"
                . "Hi {{1}}, great news! Your order with {$organization->name} has been confirmed and is currently being prepared.\n\n"
                . "📦 **Order ID:** {{2}}\n"
                . "💰 **Total Amount:** {{3}}\n"
                . "🚚 **Estimated Delivery:** {{4}}\n\n"
                . "*Footer:* Thank you for shopping with {$organization->name}!\n\n"
                . "*Quick Action:* [Track Order]";
        }

        return "💡 **Wappiyo AI Assistant for {$organization->name}:**\n\n"
            . "I'm ready to assist with your WhatsApp Business operations. Here are ways I can help:\n\n"
            . "• **Campaign Copywriting:** Create high-converting Meta-compliant broadcast messages.\n"
            . "• **Customer Support Replies:** Draft polite, contextual responses to resolve tickets quickly.\n"
            . "• **Chatbot Flows:** Outline interactive multi-step decision trees for lead qualification.\n"
            . "• **Meta Template Guidelines:** Ensure your templates pass Meta's review on the first attempt.\n\n"
            . "Try asking: *\"Draft a payment reminder message for invoices due this week\"* or select one of the suggested prompts above!";
    }
}
