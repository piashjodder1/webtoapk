<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Build;
use App\Models\Setting;
use App\Notifications\BuildStatusNotification;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BuildCallbackController extends Controller
{
    /**
     * Handle the callback from GitHub Actions build.
     */
    public function handle(Request $request)
    {
        Log::info('Received build callback request', $request->all());

        // Validate the request
        $validator = Validator::make($request->all(), [
            'build_id' => 'required|exists:builds,id',
            'status' => 'required|in:completed,failed,building',
            'github_run_id' => 'nullable|string',
            'apk_file' => 'nullable|file',
            'aab_file' => 'nullable|file',
            'apk_url' => 'nullable|string|url',
            'aab_url' => 'nullable|string|url',
            'build_log' => 'nullable|string',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Verify Callback Token (hash_equals prevents timing attacks)
        $systemToken = Setting::get('build_callback_token', 'default_callback_secret_token_123');

        if (!hash_equals((string) $systemToken, (string) $request->input('token'))) {
            Log::warning('Build callback token mismatch');
            return response()->json(['error' => 'Unauthorized token'], 401);
        }

        $build = Build::find($request->input('build_id'));
        $app = $build->app;
        $user = $app->user;

        $build->github_run_id = $request->input('github_run_id', $build->github_run_id);

        if ($request->input('status') === 'failed') {
            $build->build_status = 'failed';
            $build->build_log = $request->input('build_log', 'Unknown build error occurred.');
            $build->save();

            $app->build_status = 'failed';
            $app->save();

            // Notify user
            $user->notify(new BuildStatusNotification($build));

            return response()->json(['message' => 'Build status updated to failed.']);
        }

        if ($request->input('status') === 'building') {
            $build->build_status = 'building';
            if ($request->has('build_log')) {
                $build->build_log = $request->input('build_log');
            }
            $build->save();

            $app->build_status = 'building';
            $app->save();

            return response()->json(['message' => 'Build status updated to building.']);
        }

        // Processing 'completed' status with potential file uploads or URLs
        $storageService = new StorageService();

        if ($request->hasFile('apk_file')) {
            $apkPath = $storageService->uploadApk($request->file('apk_file'), $app->package_name);
            $build->apk_url = $storageService->getUrl($apkPath);
            $app->apk_url = $build->apk_url;
        } elseif ($request->filled('apk_url')) {
            $build->apk_url = $request->input('apk_url');
            $app->apk_url = $build->apk_url;
        }

        if ($request->hasFile('aab_file')) {
            $aabPath = $storageService->uploadAab($request->file('aab_file'), $app->package_name);
            $build->aab_url = $storageService->getUrl($aabPath);
            $app->aab_url = $build->aab_url;
        } elseif ($request->filled('aab_url')) {
            $build->aab_url = $request->input('aab_url');
            $app->aab_url = $build->aab_url;
        }

        $build->build_status = 'completed';
        $build->build_log = 'Build completed successfully. Files are ready for download.';
        $build->save();

        $app->build_status = 'completed';
        $app->save();

        // Send notifications
        $user->notify(new BuildStatusNotification($build));

        return response()->json([
            'message' => 'Build status updated to completed.',
            'apk_url' => $build->apk_url,
            'aab_url' => $build->aab_url
        ]);
    }
}
