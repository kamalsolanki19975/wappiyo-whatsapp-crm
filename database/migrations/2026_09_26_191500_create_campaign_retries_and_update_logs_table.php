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
        Schema::table('campaign_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('campaign_logs', 'retry_count')) {
                $table->unsignedInteger('retry_count')->default(0)->after('status')->index();
            }
            if (!Schema::hasColumn('campaign_logs', 'last_retried_at')) {
                $table->timestamp('last_retried_at')->nullable()->after('retry_count');
            }
            if (!Schema::hasColumn('campaign_logs', 'retry_status')) {
                $table->string('retry_status', 30)->nullable()->after('last_retried_at')->index();
            }
            if (!Schema::hasColumn('campaign_logs', 'is_excluded')) {
                $table->boolean('is_excluded')->default(false)->after('retry_status')->index();
            }
        });

        if (!Schema::hasTable('campaign_retries')) {
            Schema::create('campaign_retries', function (Blueprint $table) {
                $table->id();
                $table->string('uuid', 36)->unique();
                $table->unsignedBigInteger('campaign_id')->index();
                $table->unsignedBigInteger('campaign_log_id')->index();
                $table->unsignedBigInteger('organization_id')->index();
                $table->unsignedInteger('attempt_number')->default(1);
                $table->string('status', 30)->default('queued')->index(); // queued, processing, success, failed
                $table->string('error_code', 50)->nullable()->index();
                $table->string('failure_reason', 150)->nullable();
                $table->text('error_message')->nullable();
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('retried_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
                $table->foreign('campaign_log_id')->references('id')->on('campaign_logs')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_retries');

        Schema::table('campaign_logs', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('campaign_logs', 'retry_count')) $columns[] = 'retry_count';
            if (Schema::hasColumn('campaign_logs', 'last_retried_at')) $columns[] = 'last_retried_at';
            if (Schema::hasColumn('campaign_logs', 'retry_status')) $columns[] = 'retry_status';
            if (Schema::hasColumn('campaign_logs', 'is_excluded')) $columns[] = 'is_excluded';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
