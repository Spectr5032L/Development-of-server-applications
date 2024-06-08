<?php

namespace App\Http\Controllers;

use App\Http\DTO\UserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UsersAndRoles;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\log;

class MajorController extends Controller
{

    public function login(LoginRequest $request)
    {
        DB::beginTransaction();

        try {
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
                DB::commit();
                return response()->json(['token' => $token], 200);
            }

            DB::rollback();
            return response()->json(['error' => ' (login) Неправильный логин или пароль'], 401);
        } catch (Exception $e) {
            DB::rollback();

            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => ' (login) Ошибка аутентификации'], 500);
        }
    }
    

    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
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

            DB::commit();
            return response()->json([' (register) Созданный пользователь:' => $user], 201);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка регистрации пользователя'], 500);
        }
    }

    public function infUser(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(["user" => $user]);
    }

    public function out()
    {
        DB::beginTransaction();

        try {
            Auth::user()->currentAccessToken()->delete();

            DB::commit();
            return response()->json(['message' => ' (out) Вы успешно разлогинились'], 200);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка выхода из системы'], 500);
        }
    }

    public function tokens()
    {
        DB::beginTransaction();

        try {
            PersonalAccessToken::where('expires_at', '<', now())->delete();

            DB::commit();
            return response()->json(['tokens' => Auth::user()->tokens->pluck('token')]);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка получения токенов'], 500);
        }
    }

    public function outAll()
    {
        DB::beginTransaction();

        try {
            Auth::user()->tokens()->delete();
            
            DB::commit();
            return response()->json(['message' => ' (outAll) Все токены отозваны'], 200);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка выхода из системы'], 500);
        }
    }
}
