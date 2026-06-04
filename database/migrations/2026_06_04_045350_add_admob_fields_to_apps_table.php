<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->string('admob_app_id')->nullable()->after('enable_admob');
            $table->string('admob_banner_unit_id')->nullable()->after('admob_app_id');
            $table->string('admob_interstitial_unit_id')->nullable()->after('admob_banner_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn(['admob_app_id', 'admob_banner_unit_id', 'admob_interstitial_unit_id']);
        });
    }
};
