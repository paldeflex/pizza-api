<?php

namespace App\Http\Controllers\Api;

use App\Dto\JwtTokenDto;
use App\Dto\LoginDto;
use App\Dto\RegisterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        $registerDto = new RegisterDto(
            $request->string('name'),
            $request->string('email'),
            $request->string('password')
        );

        $user = $this->userService->createUser($registerDto);
        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $loginDto = new LoginDto(
            $request->string('email'),
            $request->string('password')
        );

        if (! $token = auth()->attempt($loginDto->toArray())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        $jwtTokenDto = new JwtTokenDto(
            $token,
            'bearer',
            auth()->factory()->getTTL() * 60
        );

        return response()->json($jwtTokenDto->toArray());
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);

    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }
}
