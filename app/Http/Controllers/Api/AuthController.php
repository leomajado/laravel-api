<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use App\Models\User;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{

    public function signUp(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string'
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            $header = ['Content-Type: application/json'];

            return response()->json([
                'status' => 'ok',
                'message' => 'Successfully created User.'
            ], 200,$header);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ], 500,$header);
        }
    }

    public function login(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
                'remember_me' => 'boolean'
            ]);

            $credentials = request(['email', 'password']);

            $header = ['Content-Type: application/json'];

            if (!Auth::attempt($credentials))
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401,$header);

            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
            ],200,$header);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ], 500,$header);
        }
    }

    public function logout(Request $request)
    {
        try {

            $request->user()->token()->revoke();

            $header = ['Content-Type: application/json'];

            return response()->json([
                'status' => 'ok',
                'message' => 'Successfully logged out.'
            ],200,$header);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ],500,$header);
        }

    }

    public function user(Request $request)
    {
        try {

            $header = ['Content-Type: application/json'];

            return response()->json($request->user(),200,$header);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ],200,$header);
        }

    }

}
