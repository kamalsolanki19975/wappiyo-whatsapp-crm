<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure organizations timezone column has default 'Asia/Kolkata'
        if (Schema::hasColumn('organizations', 'timezone')) {
            try {
                DB::statement("ALTER TABLE `organizations` ALTER `timezone` SET DEFAULT 'Asia/Kolkata'");
            } catch (\Exception $e) {
                // If DB engine doesn't support ALTER SET DEFAULT directly, ignore or alter column
            }
        }

        // 2. Set system default timezone setting in settings table to Asia/Kolkata if missing or UTC
        $tzSetting = DB::table('settings')->where('key', 'timezone')->first();
        if ($tzSetting) {
            if ($tzSetting->value === 'UTC' || empty($tzSetting->value)) {
                DB::table('settings')->where('key', 'timezone')->update(['value' => 'Asia/Kolkata']);
            }
        } else {
            DB::table('settings')->insert([
                'key' => 'timezone',
                'value' => 'Asia/Kolkata',
            ]);
        }

        // 3. For existing organizations, safely default missing/empty/invalid timezones to Asia/Kolkata
        $validTimezones = array_flip(timezone_identifiers_list());
        $organizations = DB::table('organizations')->select('id', 'timezone', 'metadata')->get();

        foreach ($organizations as $org) {
            $colTz = trim($org->timezone ?? '');
            $metadata = !empty($org->metadata) ? json_decode($org->metadata, true) : [];
            $metaTz = trim($metadata['timezone'] ?? '');

            $effectiveTz = null;

            // Preserve existing valid timezone if present
            if (!empty($colTz) && isset($validTimezones[$colTz])) {
                $effectiveTz = $colTz;
            } elseif (!empty($metaTz) && isset($validTimezones[$metaTz])) {
                $effectiveTz = $metaTz;
            } else {
                $effectiveTz = 'Asia/Kolkata';
            }

            $updateData = [];

            if ($colTz !== $effectiveTz) {
                $updateData['timezone'] = $effectiveTz;
            }

            if (($metadata['timezone'] ?? null) !== $effectiveTz) {
                $metadata['timezone'] = $effectiveTz;
                $updateData['metadata'] = json_encode($metadata);
            }

            if (!empty($updateData)) {
                DB::table('organizations')->where('id', $org->id)->update($updateData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert system setting if desired
    }
};
