<?php

namespace App\Http\Requests;

use App\Models\Bucket;
use Illuminate\Foundation\Http\FormRequest;

class FileStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $request = request();

        $name = $request->input('bucket');
        $bucket = Bucket::where('name', $name)->first();
        if (!$bucket) {
            return false;
        }

        $key = $request->input('authKey');
        // Get bearer token
        $secret = $request->bearerToken();

        if ($bucket->authKey !== $key || $bucket->authSecret !== $secret) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:files',
            'type' => 'required|string',
            'file' => 'required|file',
            'bucket' => 'required|string'
        ];
    }
}
