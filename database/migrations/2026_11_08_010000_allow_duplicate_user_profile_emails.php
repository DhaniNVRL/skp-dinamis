<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_profiles')
            && Schema::hasIndex('user_profiles', 'user_profiles_email_unique')) {
            Schema::table('user_profiles', function (Blueprint $table): void {
                $table->dropUnique('user_profiles_email_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_profiles')
            && ! Schema::hasIndex('user_profiles', 'user_profiles_email_unique')) {
            Schema::table('user_profiles', function (Blueprint $table): void {
                $table->unique('email');
            });
        }
    }
};
