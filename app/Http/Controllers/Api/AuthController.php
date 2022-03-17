<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use App\Models\User;

class AuthController extends Controller {

    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         example="madeline13@example.net"
     *       ),
     *       @OA\Property(
     *         property="password",
     *         type="string",
     *         format="password",
     *         example="password"
     *       ),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Sorry, wrong email address or password. Please try again"
     *       )
     *    )
     *   )
     * )
     */
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

    /**
     * @OA\Get(
     * path="/api/logout",
     * summary="Sign out",
     * description="Logout by token",
     * operationId="authLogout",
     * tags={"auth"},
     * security={{"bearerAuth": {}}},
     * @OA\Response(
     *   response="200",
     *   description="OK, Token Revoked",
     *   @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Token Revoked Successfully"
     *       )
     *    )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong user data",
     *    @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Sorry, Unauthenticated User."
     *       )
     *     )
     *   )
     * )
     */
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

    /**
     * @OA\Get(
     * path="/api/user",
     * summary="User Data",
     * description="Show User Details.",
     * operationId="authUser",
     * tags={"auth"},
     * security={{"bearerAuth": {}}},
     * @OA\Response(
     *    response=422,
     *    description="User",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, No user data. Please try again")
     *        )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     * path="/api/signup",
     * summary="Sign in",
     * description="Sign Up User",
     * operationId="authSignUp",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name","email","password"},
     *       @OA\Property(
     *         property="name",
     *         type="string",
     *         format="text",
     *         example="New User Name"
     *       ),
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         example="new@example.net"
     *       ),
     *       @OA\Property(
     *         property="password",
     *         type="string",
     *         format="password",
     *         example="password"
     *       ),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="user created successfully",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *     )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="User Creation Error",
     *    @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Sorry, user can't be created"
     *       )
     *    )
     *   )
     * )
     */
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
}
