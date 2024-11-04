<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\BearerAccessTokens;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $bearerToken;

    /**
     * Create a new AuthController instance.
     *
     *
     * @return void
     */
    public function __construct(BearerAccessTokens $bearerToken)
    {
        $this->bearerToken = $bearerToken;
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'register']]);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = request()->only(['email', 'password']);

        // $user = User::whereEmail($credentials['email'])->first();
        if(Auth::attempt($credentials)){ 
            $user = Auth::user();
            
            // if(!$user->isAdmin()) {
            //     \Auth::logout();
            //     return response()->json(['error' => 'Unauthorized Invalid'], Response::HTTP_UNAUTHORIZED);
            // }

            $mergeidwithname = $user->id.$user->name;
            $tokenResult = $user->createToken($mergeidwithname);
            $token = $tokenResult->accessToken;

            // Assuming $this->bearerToken is injected as BearerAccessTokens
            $this->bearerToken->setToken($token);
            $this->bearerToken->setUser($user);
            $this->bearerToken->save();

            return $this->respondWithToken($token);
        }else{
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // if (! Auth::guard('web')->attempt($credentials, false, false)) {
        // } else { 
        // }
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        // Get the TTL from config or set a default value (e.g., 60 minutes)
        $expiresIn = config('auth.guards.api.ttl', 60) * 60; // Default is 60 minutes

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiresIn,
        ]);
    }

    public function register()
    {
        $data = request()->all();

        try {
            request()->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            return response()->json([
                'message' => sprintf('User successful created. You can generate auth token.'),
                'data' => [
                    'email' => $user->email,
                    'name' => $user->name,
                ],
            ]);
        } catch (ValidationException $e) {
            return \response()->json([
                'message' => $e->getMessage(),
                'errors' => $e
                    ->validator
                    ->errors()
                    ->messages(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return \response()->json(['message' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
