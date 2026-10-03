<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    protected User $user;
    protected Organization $org;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'first_name' => 'AI',
            'last_name' => 'Tester',
            'email' => 'ai.tester.' . Str::random(8) . '@wappiyo.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->org = Organization::create([
            'identifier' => 'ai-org-' . Str::random(6),
            'name' => 'AI Test Workspace',
            'created_by' => $this->user->id,
        ]);

        Team::create([
            'user_id' => $this->user->id,
            'organization_id' => $this->org->id,
            'role' => 'owner',
            'created_by' => $this->user->id,
        ]);

        \App\Models\Subscription::create([
            'organization_id' => $this->org->id,
            'status' => 'active',
            'plan_id' => null,
            'start_date' => now(),
            'valid_until' => now()->addDays(30),
        ]);
    }

    public function test_ai_assistant_screen_returns_200_no_404(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->get('/automation/ai');
        $response->assertStatus(200);

        $altResponse = $this->actingAs($this->user, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->get('/ai-assistant');
        $altResponse->assertStatus(200);
    }

    public function test_ai_chat_api_returns_structured_response(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->postJson('/automation/ai/chat', [
                'message' => 'Help me write a broadcast message for Diwali sale',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('reply'));
    }

    public function test_ai_chat_requires_message(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->postJson('/automation/ai/chat', [
                'message' => '',
            ]);

        $response->assertStatus(422);
    }

    public function test_ai_chat_clear_session(): void
    {
        $response = $this->actingAs($this->user, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->postJson('/automation/ai/clear');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
