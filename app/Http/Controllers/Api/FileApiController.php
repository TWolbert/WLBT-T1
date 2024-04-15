<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FileStoreRequest;
use App\Models\Bucket;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FileApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request = request();
        $skipCache = $request->query('skip_cache', false);

        if (Cache::has('files') && !$skipCache) {
            return response()->json(Cache::get('files'));
        }

        $files = File::all();
        Cache::put('files', $files, 60);
        return response()->json($files);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileStoreRequest $request)
    {
        $file = new File();
        $file->name = $request->name;
        $file->type = $request->type;
        $file->uploaded_from = $request->ip();
        $blob = $request->validated('file');
        $file->size = strlen($blob);
        $file->path = $request->validated('bucket') . '/' . $file->name;

        Storage::putFileAs($request->bucket, $blob, $file->name);
        
        $file->save();

        return response()->json($file);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        $request = request();
        $bucketName = $request->input('bucket');
        $key = $request->input('authKey');
        $secret = $request->input('authSecret');

        $bucket = Bucket::where('name', $bucketName)->first();

        if (!$bucket || $bucket->authKey !== $key || $bucket->authSecret !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get file from storage
        $fileContent = Storage::get($file->path);

        // Send as octet-stream
        return response($fileContent)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename=' . $file->name);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        //
    }
}
