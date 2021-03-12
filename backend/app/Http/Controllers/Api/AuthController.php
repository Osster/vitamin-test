<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $tokens = collect(Auth::user()->tokens);

            if ($tokens->count() > 0) {

               $token = $tokens->first();

            } else {

                $token = $request->user()->createToken('Auth');

            }

            return response()
                ->json([
                    "result" => "OK",
                    "token" => $token->plainTextToken
                ], 200)
                ->header('Authorization', "Bearer {$token->plainTextToken}");

        }

        return response()->json(["result" => "FAIL"]);
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if (!Auth::guest()) {

            Auth::user()->tokens()->delete();

            Auth::guard('web')->logout();

            //Auth::logout();

            return response()->json(["result" => "OK"]);
        }

        return response()->json(["result" => "FAIL"]);
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
