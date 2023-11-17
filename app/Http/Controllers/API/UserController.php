<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            // code...
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password],
            ]);

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('name', $request->name)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
                'User Registered'
            ]);
        } catch (Exception $e) {
            // throw $th;
            return ResponseFormatter::error(
                [
                    'message' => 'something get wrong',
                    'Error' => $e
                ],
                'Authenticate Failed',
                500
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);
            $credentials = $request->only(['email','password']);
            if (!Auth::attempt($credentials)) {
                # code...
                return ResponseFormatter::error(
                    [
                        'message' => 'Fail',
                        'error' => 'Unauthorized'
                    ],
                    'Authenticate failed',
                    500
                );
            }
            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('')->plainTextToken;
            return ResponseFormatter::success([
                'message' => $tokenResult,
                'success' => 'berhasil login',
                'user' => $user,
            ], 'Berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => 'Fail',
                    'error' => $e
                ],
                'Authenticate error',
                500
            );
        }
    }

    public function fetch(Request $request){
        return ResponseFormatter::success($request->user(), 'Data Login Berhasil di Fetch');
    }
    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Data Token Berhasil dihapus');
    }
}
