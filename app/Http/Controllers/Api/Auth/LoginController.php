<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * LOGIN
     */
    public function index(Request $request)
    {
        // VALIDASI
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // MENGEMBALIKAN EROR JIKA VALIDASI TIDAK SESUAI
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // GET EMAIL DAN PASSWORD
        $credentials = $request->only('email', 'password');

        // CEK JIKA EMAIL ATAU PASSWORD SALAH
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Kata Sandi salah'
            ], 401); // 401 Unauthorized
        }

        // KEMBALIKAN JIKA SEMUA BENAR SUKSES DAN GENERATE TOKEN JWT
        return response()->json([
            'success'       => true,
            'user'          => auth()->guard('api')->user()->only(['name', 'email']),
            'token'         => $token
        ], 200); // 200 OK
    }
}
