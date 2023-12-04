<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\HelperTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use App\Models\User;

use Exception;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
  use HelperTrait;

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
   *         example="user@example.com"
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

      // Credential validation format required
      $credentials = $request->validate([
        'email' => [
          'required',
          'string',
          'email',
          'max:255',
        ],
        'password' => [
          'required',
          'string',
          'min:8',
        ],
      ]);

      // Authentication Check
      if (!$token = JWTAuth::attempt($credentials)) {
        throw new JWTException("Invalid Credentials", 401);
      }

      // Return Success json Object with User Role
      return response()->json([
        'status' => 'ok',
        'access_token' => $token,
        'token_type' => 'Bearer',
      ], 200, $this->getJsonHeader());
    } catch (JWTException $ex) {
      return response()->json(
        [
          'status' => 'error',
          'data' => $ex->getMessage()
        ],
        $ex->getCode() == 0 ?
          404 : $ex->getCode(),
        $this->getJsonHeader()
      );
    }
  }

  /**
   * @OA\Post(
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

      // Kill token and logout user
      if (!Auth::check()) {
        throw new JWTException("Unauthenticated User", 403);
      }

      Auth::logout();

      return response()->json([
        'status' => 'ok',
        'data' => 'Token Revoked Successfully'
      ], 200, $this->getJsonHeader());
    } catch (Exception $ex) {
      return response()->json(
        [
          'status' => 'error',
          'data' => $ex->getMessage()
        ],
        $ex->getCode() == 0 ?
          404 : $ex->getCode(),
        $this->getJsonHeader()
      );
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

      if (!Auth::check()) {
        throw new Exception("Unauthorized user", 403);
      }

      $user = User::find(Auth::user()->id);

      return response()->json($user);
    } catch (Exception $ex) {

      return response()->json(
        [
          'status' => 'error',
          'data' => $ex->getMessage()
        ],
        $ex->getCode() == 0 ?
          404 : $ex->getCode(),
        $this->getJsonHeader()
      );
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

      $user = new User();
      $user->name = $request->name;
      $user->email = $request->email;
      $user->password = bcrypt($request->password);
      $user->email_verified_at = now();
      $user->remember_token = Str::random(10);
      $user->save();

      $header = ['Content-Type: application/json'];

      return response()->json([
        'status' => 'ok',
        'message' => 'Successfully created User.'
      ], 200, $header);
    } catch (\Exception $ex) {
      $header = ['Content-Type: application/json'];
      return response()->json([
        'status' => 'error',
        'message' => $ex->getMessage()
      ], 500, $header);
    }
  }
}
