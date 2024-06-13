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

use PHPMailer\PHPMailer\PHPMailer;

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
        
                switch (self::confirmCode($loginDTO->tfa_code))
                {
                    case 'Код не действителен':
                        DB::commit();
                        return self::getCode(); # новый код

                    case 'Код не подтверждён':
                        return response()->json(['message' => 'Неверный код'], 422);

                    case 'Код подтверждён':
                        $user->tfa_code_count = 0;
                        $user->save();

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
                            ->addMinutes(env('TIME_LIVE_TOKEN', 10)))->plainTextToken;
                        DB::commit();
                        return response()->json(['token' => $token], 200);
                }
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

    public function getCode()
    {
        $user = Auth::user();
        $user->tfa_code_count += 1;
        $user->tfa_code = null;
        $user->tfa_code_valid_until = null;
        $user->save();

        if ($user->tfa_code_count > 3)
        {
            if ($user->delay_until == null)
            {
                $user->delay_until = Carbon::now()->addSeconds(30);
                $user->save();
            }

            if (Carbon::now()->lt($user->delay_until))
            {
                return response()->json(['message' => 'Подождите']);
            }
        }

        $user->tfa_code = mt_rand(100000, 999999);
        $user->tfa_code_valid_until = Carbon::now()->addSeconds(env('TWO_FACTOR_CODE_EXPIRATION', 60));
        $user->save();

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_USERNAME');
        $mail->Password = env('MAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = env('MAIL_PORT');
        $mail->setFrom(env('MAIL_USERNAME'));
        $mail->addAddress("mihail.plotnikov.08@mail.ru"); // Всегда на этот ($user->email)
        $mail->isHTML(false);
        $mail->Subject = 'tfa code';
        $mail->Body = $user->tfa_code;

        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';

        $mail->send();

        return response()->json(['tfa_code' => $user->tfa_code]);
    }

    private function confirmCode($tfa_code)
    {
        $user = Auth::user();

        if ($user->tfa_code && Carbon::now()->lt($user->tfa_code_valid_until))
        {
            if ($tfa_code == $user->tfa_code)
            {
                return 'Код подтверждён';
            }
            return 'Код не подтверждён';
        }
        return 'Код не действителен';
    }
}
