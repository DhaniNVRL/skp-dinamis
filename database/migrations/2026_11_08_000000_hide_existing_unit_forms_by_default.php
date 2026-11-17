<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('unit_form_visibilities')) {
            DB::table('unit_form_visibilities')->update(['is_visible' => false]);
        }
    }

    public function down(): void
    {
        // Status sebelumnya tidak dapat dipulihkan dengan aman.
    }
};
