<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('answers', function (Blueprint $table): void {
            $table->index(['user_id', 'form_id'], 'answers_user_form_export_index');
            $table->index(['form_id', 'subunit_id', 'user_id'], 'answers_form_subunit_user_export_index');
        });

        Schema::table('user_profiles', function (Blueprint $table): void {
            $table->index(['activity_id', 'group_id', 'id'], 'profiles_activity_group_export_index');
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table): void {
            $table->dropIndex('answers_user_form_export_index');
            $table->dropIndex('answers_form_subunit_user_export_index');
        });

        Schema::table('user_profiles', function (Blueprint $table): void {
            $table->dropIndex('profiles_activity_group_export_index');
        });
    }
};
