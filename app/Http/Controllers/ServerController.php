<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if the cache exists
        if (Cache::has('server_info')) {
            return response()->json(Cache::get('server_info'));
        }

        // Return php version and laravel version
        $info = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
        ];

        Cache::put('server_info', $info, 60);

        return response()->json($info);
    }

    public function createBucket(Request $request)
    {
        $bucket = new Bucket();
        $bucket->name = $request->name;
        $randomAuthKey = bin2hex(random_bytes(16));
        $bucket->authKey = $randomAuthKey;
        $randomAuthSecret = bin2hex(random_bytes(16));
        $bucket->authSecret = $randomAuthSecret;

        $bucket->save();

        // Create folder in storage
        Storage::makeDirectory($bucket->name);

        return response()->json($bucket);
    }

    public function check(Request $request) {
        $name = $request->input('bucketName');
        $bucket = Bucket::where('name', $name)->first();
        if (!$bucket) {
            return response()->json(['message' => 'Bucket not found'], 404);
        }

        // Check auth
        $key = $request->input('authKey');
        $secret = $request->input('authSecret');

        if ($bucket->authKey !== $key || $bucket->authSecret !== $secret) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json($bucket);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $bucketName)
    {
        Bucket::where('name', $bucketName)->delete();
    }
}
