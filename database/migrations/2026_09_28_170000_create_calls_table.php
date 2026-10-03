<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 50)->unique();
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('contact_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('whatsapp_account_id', 128)->nullable();
            $table->string('phone_number_id', 128)->nullable();
            $table->string('customer_phone', 64)->index();
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound')->index();
            $table->string('provider', 64)->default('meta');
            $table->string('provider_call_id', 128)->nullable()->index();
            $table->string('status', 64)->default('initiating')->index();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->default(0); // in seconds
            $table->text('failure_reason')->nullable();
            $table->string('failure_code', 64)->nullable();
            $table->string('recording_reference', 255)->nullable();
            $table->text('recording_url')->nullable();
            $table->string('disposition', 128)->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('follow_up_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamp('deleted_at')->nullable()->index();
            $table->timestamps();

            // Composite indexes for fast call history querying and analytics
            $table->index(['organization_id', 'created_at']);
            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'direction']);
            $table->index(['organization_id', 'contact_id']);
            $table->index(['organization_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
