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
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'enable_push_notification',
                'onesignal_app_id',
                'enable_admob',
                'admob_app_id',
                'admob_banner_unit_id',
                'admob_interstitial_unit_id',
            ]);
        });

        \Illuminate\Support\Facades\DB::table('settings')->where('group', 'admob')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->boolean('enable_push_notification')->default(false)->after('enable_offline_page');
            $table->string('onesignal_app_id')->nullable()->after('enable_push_notification');
            $table->boolean('enable_admob')->default(false)->after('onesignal_app_id');
            $table->string('admob_app_id')->nullable()->after('enable_admob');
            $table->string('admob_banner_unit_id')->nullable()->after('admob_app_id');
            $table->string('admob_interstitial_unit_id')->nullable()->after('admob_banner_unit_id');
        });
    }
};
