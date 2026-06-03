<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Build extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'github_run_id',
        'build_type',
        'build_status',
        'apk_url',
        'aab_url',
        'build_log',
    ];

    /**
     * Get the app that owns the build.
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
