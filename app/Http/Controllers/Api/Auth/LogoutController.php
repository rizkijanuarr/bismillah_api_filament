<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * LOGOUT
     */
    public function index()
    {
        // HAPUS TOKEN JWT
        JWTAuth::invalidate(JWTAuth::getToken());

        // KEMBALIKAN SUKSES JIKA LOGOUT
        return response()->json([
            'success' => true,
        ], 200); // 200 OK
    }
}
