<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
     * Display the specified resource.
     */
    public function show(ApiKey $apiKey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApiKey $apiKey)
    {
        //
    }

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
