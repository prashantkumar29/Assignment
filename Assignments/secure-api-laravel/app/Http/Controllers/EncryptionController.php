<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EncryptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function encrypt(Request $request)
    {
        $user = Auth::user();
        $key = Cache::get('encryption_key_' . $user->id);
        $data = $request->input('data');
        $encrypted = base64_encode(openssl_encrypt(json_encode($data), 'AES-256-CBC', $key, 0, substr($key, 0, 16)));
        return response()->json(['encrypted' => $encrypted]);
    }

    public function decrypt(Request $request)
    {
        $user = Auth::user();
        $key = Cache::get('encryption_key_' . $user->id);
        $encrypted = $request->input('encrypted');
        $decrypted = openssl_decrypt(base64_decode($encrypted), 'AES-256-CBC', $key, 0, substr($key, 0, 16));
        return response()->json(['decrypted' => json_decode($decrypted, true)]);
    }
}
