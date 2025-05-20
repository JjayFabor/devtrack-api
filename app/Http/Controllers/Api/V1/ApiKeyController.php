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
 * @header Authorization string required Example: "Bearer {YOUR_API_TOKEN}"
 */
class ApiKeyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Revoke the API.
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
     * Remove the specified resource from storage.
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
