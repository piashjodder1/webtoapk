<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static function up(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->string('onesignal_app_id')->nullable()->after('enable_push_notification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public static function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn('onesignal_app_id');
        });
    }
};
