<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->boolean('enable_push_notification')->default(false)->after('enable_offline_page');
            $table->string('onesignal_app_id')->nullable()->after('enable_push_notification');
        });
    }

    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn(['enable_push_notification', 'onesignal_app_id']);
        });
    }
};
