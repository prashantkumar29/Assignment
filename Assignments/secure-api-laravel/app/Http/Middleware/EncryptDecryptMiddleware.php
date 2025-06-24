<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EncryptDecryptMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            $key = Cache::get('encryption_key_' . $user->id);
            if ($key && $request->isJson()) {
                $data = $request->json()->all();
                if (isset($data['encrypted'])) {
                    $decrypted = openssl_decrypt(base64_decode($data['encrypted']), 'AES-256-CBC', $key, 0, substr($key, 0, 16));
                    $request->merge(json_decode($decrypted, true));
                }
            }
        }
        $response = $next($request);
        if ($user && $key && $response->isSuccessful() && $response->headers->get('Content-Type') === 'application/json') {
            $encrypted = base64_encode(openssl_encrypt($response->getContent(), 'AES-256-CBC', $key, 0, substr($key, 0, 16)));
            $response->setContent(json_encode(['encrypted' => $encrypted]));
        }
        return $response;
    }
}
