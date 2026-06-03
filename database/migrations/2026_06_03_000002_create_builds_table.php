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
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained()->onDelete('cascade');
            $table->string('github_run_id')->nullable();
            $table->string('build_type')->default('both'); // apk, aab, both
            $table->string('build_status')->default('pending'); // pending, queued, building, completed, failed
            $table->string('apk_url')->nullable();
            $table->string('aab_url')->nullable();
            $table->longText('build_log')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('builds');
    }
};
