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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('app_name');
            $table->string('website_url');
            $table->string('package_name');
            $table->string('icon_path')->nullable();
            $table->string('splash_path')->nullable();
            $table->boolean('enable_pull_refresh')->default(false);
            $table->boolean('enable_offline_page')->default(false);
            $table->boolean('enable_push_notification')->default(false);
            $table->boolean('enable_admob')->default(false);
            $table->string('apk_url')->nullable();
            $table->string('aab_url')->nullable();
            $table->string('build_status')->default('pending'); // pending, queued, building, completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
