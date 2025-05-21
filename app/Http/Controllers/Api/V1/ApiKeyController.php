<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @group API Key Management
 *
 * APIs for managing API keys.
 *
 * @authenticated
 * @header Authorization Bearer {YOUR ACCESS TOKEN}
 */
class ApiKeyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Retrieve a list of API keys
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'API keys retrieved successfully',
            'data' => Auth::user()->apiKeys,
        ], 200);
    }

    /**
     * Generate a new API key
     *
     * @bodyParam name string The name of the API key. Example: "test-name-api-key"
     * @bodyParam expires_at datetime nullable When the key expires. Example: null
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        $plainKey = bin2hex(random_bytes(16));
        $hashedKey = hash('sha256', $plainKey);

        $apiKey = ApiKey::create([
            'user_id' => Auth::id(),
            'key' => $hashedKey,
            'name' => $request->name,
            'expires_at' => $request->expires_at,
            'ip_address' => $request->getClientIp(),
        ]);


        return response()->json([
            'success' => true,
            'message' => 'API key generated successfully',
            'key' => $plainKey,
        ], 201);
    }

    /**
     * Retrieve a specific API key
     *
     */
    public function show(ApiKey $apiKey)
    {
        return response()->json([
            'success' => true,
            'message' => 'API key retrieved successfully',
            'data' => $apiKey,
        ], 200);
    }

    /**
     * Regenerate the API key
     *
     * Generates a new plain API key for the given API key record, updates the hash in the database,
     * and returns the new plain key. The old key will no longer be valid.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "API key regenerated successfully",
     *   "key": "newplainapikey123..."
     * }
     */
    public function regenerate(ApiKey $apiKey)
    {
        $this->authorize('update', $apiKey);

        $plainKey = bin2hex(random_bytes(16));
        $hashedKey = hash('sha256', $plainKey);

        $apiKey->update([
            'key' => $hashedKey,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'API key regenerated successfully',
            'key' => $plainKey,
        ], 200);
    }

    /**
     * Revoke the API
     *
     * Revokes the API key by setting its `is_active` status to false.
     */
    public function revoke(ApiKey $apiKey)
    {
        $this->authorize('delete', $apiKey);
        $apiKey->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'API key revoked successfully',
        ], 200);
    }

    /**
     * Remove the API key
     */
    public function destroy(ApiKey $apiKey)
    {
        $this->authorize('delete', $apiKey);
        $apiKey->delete();

        return response()->json([
            'success' => true,
            'message' => 'API key deleted successfully',
        ], 200);
    }
}
