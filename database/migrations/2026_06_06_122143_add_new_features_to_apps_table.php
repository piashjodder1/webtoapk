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
            $table->boolean('enable_exit_confirmation')->default(false);
            $table->boolean('enable_loading_progress_bar')->default(false);
            $table->boolean('enable_external_links_in_browser')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'enable_exit_confirmation',
                'enable_loading_progress_bar',
                'enable_external_links_in_browser',
            ]);
        });
    }
};
