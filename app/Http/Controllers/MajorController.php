<?php

namespace App\Http\Controllers;

use App\Http\DTO\UserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UsersAndRoles;
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
    
            $activeTokens = $user->tokens()->orderBy('created_at')->get();
            $maxActiveTokens = env('COUNTS_ACTIVE_TOKENS', 3);
    
            if ($activeTokens->count() >= $maxActiveTokens)
            {
                $tokensToDelete = $activeTokens->take($activeTokens->count() - $maxActiveTokens + 1);
                foreach ($tokensToDelete as $token) {
                    $token->delete();
                }
            }

            $token = $user->createToken($loginDTO->username . '_token', ['*'], now()
                ->addMinutes(env('TIME_LIVE_TOKEN', 1)))->plainTextToken;
            return response()->json(['token' => $token], 200);
        }
    
        return response()->json(['error' => ' (login) Неправильный логин или пароль'], 401);
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
        $userAndRole = new UsersAndRoles();
        $userAndRole->user_id = $user->id;
        $userAndRole->role_id = Role::where('cipher', 'GUEST')->value('id');
        $userAndRole->created_by = $user->id;
        $userAndRole->save();

        return response()->json([' (register) Созданный пользователь:' => $user], 201);
    }

    public function infUser(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(["user" => $user]);
    }

    public function out()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json(['message' => ' (out) Вы успешно разлогинились'], 200);
    }

    public function tokens()
    {
        PersonalAccessToken::where('expires_at', '<', now())->delete();
        return response()->json(['tokens' => Auth::user()->tokens->pluck('token')]);
    }

    public function outAll()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => ' (outAll) Все токены отозваны'], 200);
    }
}
