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
        Schema::table('otps', function (Blueprint $table) {
            $table->string('email')->nullable()->index()->after('id');
            $table->string('phone')->nullable()->change();
            $table->timestamp('expires_at')->nullable()->after('otp');
            $table->unsignedTinyInteger('attempts')->default(0)->after('expires_at');
            $table->timestamp('verified_at')->nullable()->after('attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->dropColumn(['email', 'expires_at', 'attempts', 'verified_at']);
        });
    }
};
