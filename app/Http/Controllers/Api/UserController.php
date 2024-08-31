<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiFormater as ResourcesApiFormater;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // INDEX
    public function index()
    {
        $users = User::latest()->paginate(5);
        return new ResourcesApiFormater(true, 'List Data Users', $users);
    }

    // CREATE
    public function store(Request $request)
    {
        // VALIDASI
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // MENGEMBALIKAN EROR JIKA VALIDASI TIDAK SESUAI
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // MEMBUAT USER
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password'  => bcrypt($request->password),
            'email_verified_at' => now(),
        ]);

        // GET EMAIL DAN PASSWORD
        $credentials = $request->only('email', 'password');

        // CEK JIKA EMAIL ATAU PASSWORD SALAH
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Kata Sandi salah'
            ], 400);
        }

        // KEMBALIKAN JIKA SEMUA BENAR SUKSES DAN GENERATE TOKEN JWT
        return response()->json([
            'success'       => true,
            'user'          => auth()->guard('api')->user()->only(['name', 'email']),
            'token'         => $token
        ], 201);
    }

    // SHOW
    public function show($id)
    {
        $user = User::whereId($id)->first();

        // JIKA SUKSES
        if ($user) {
            return new ResourcesApiFormater(true, 'User Data', $user);
        } else {
            // JIKA GAGAL
            return new ResourcesApiFormater(false, 'User Data Tidak Ditemukan!', null);
        }
    }

    // UPDATE
    public function update(Request $request, User $user)
    {
        // VALIDASI
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,'.$user->id,
            'password' => 'confirmed'
        ]);

        // KEMBALIKAN EROR JIKA VALIDASI TIDAK TERPENUHI
        if ($validator->fails()) {
            return new ResourcesApiFormater(false, 'Validation Error', $validator->errors(), 422);
        }

        // UPDATE
        if($request->password == "") {
            // UPDATE TANPA PASSWORD
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);
        } else {
            // UPDATE DENGAN PASSWORD
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);
        }

        if($user) {
             // JIKA SUKSES
            return new ResourcesApiFormater(true, 'User Berhasil Diupdate!', $user);
        } else {
             // JIKA GAGAL
            return new ResourcesApiFormater(false, 'User Gagal Diupdate!', null);
        }
    }

    // DELETE
    public function destroy(User $user)
    {
        if ($user->delete()) {
            // JIKA SUKSES
            return new ResourcesApiFormater(true, 'User Berhasil Dihapus!', null);
        } else {
            // JIKA GAGAL
            return new ResourcesApiFormater(false, 'User Gagal Dihapus!', null);
        }
    }
}
