<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('modules')->where('name', 'calling')->exists();

        if (!$exists) {
            DB::table('modules')->insert([
                'name' => 'calling',
                'actions' => 'view, make, manage, export, configure',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('modules')->where('name', 'calling')->delete();
    }
};
