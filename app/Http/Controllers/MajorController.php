<?php

namespace App\Http\Controllers;

use App\Http\DTO\UserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class MajorController extends Controller
{
    public function login(LoginRequest $request)
    {
        PersonalAccessToken::where('expires_at', '<', now())->delete();
        $loginDTO = $request->toDTO();

        if (Auth::attempt(['username' => $loginDTO->username, 'password' => $loginDTO->password]))
        {
            $user = Auth::user();

            $activeTokensCount = $user->tokens()->count();
            $maxActiveTokens = env('COUNTS_ACTIVE_TOKENS', 3);

            if ($activeTokensCount < $maxActiveTokens)
            {
                $token = $user->createToken($loginDTO->username . '_token', ['*'], now()
                    ->addMinutes(env('TIME_LIVE_TOKEN')))->plainTextToken;
                return response()->json(['token' => $token], 200);
            }

            return response()->json(['error' => 'Достигнуто максимальное кол-во активных токенов'], 403);
        }

        return response()->json(['error' => 'Неправильный логин или пароль'], 401);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $registerDTO = $request->toDTO();

        $user = new User([
            'username' => $registerDTO->username,
            'email' => $registerDTO->email,
            'password' => $registerDTO->password,
            'birthday' => $registerDTO->birthday,
        ]);

        $user->save();

        return response()->json(['Созданный пользователь:' => $user], 201);
    }

    public function infUser(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(["user" => $user]);
    }

    public function out()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Вы успешно разлогинились'], 200);
    }

    public function tokens()
    {
        PersonalAccessToken::where('expires_at', '<', now())->delete();
        return response()->json(['tokens' => Auth::user()->tokens->pluck('token')]);
    }

    public function outAll()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Все токены отозваны'], 200);
    }
}
