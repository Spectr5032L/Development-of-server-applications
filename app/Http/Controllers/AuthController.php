<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\DTO\AuthResourceDTO;
use App\Http\DTO\RegisterResourceDTO;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        // Проверяем учетные данные пользователя
        if (!auth('web')->attempt(['name' => $request->name, 'password' => $request->password])) {
            return response()->json(['message' => 'Неверные учетные данные'], 401);
        }
    
        // Получаем аутентифицированного пользователя
        $user = auth('web')->user();
    
        // Создаем токен доступа
        $token = $user->createToken('Access Token')->accessToken;
    
        // Формируем ресурс для ответа
        $resource = new AuthResourceDTO($token);
    
        // Возвращаем ресурс с кодом статуса 200
        return response()->json($resource, 200);
    }
    
    public function register(RegisterRequest $request)
    {
        // Создаем нового пользователя
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birthday' => $request->birthday,
        ]);
    
        // Формируем ресурс для ответа
        $resource = new RegisterResourceDTO($user);
    
        // Возвращаем ресурс с кодом статуса 201
        return response()->json($resource, 201);
    }

    public function me()
    {

    }

    public function logout()
    {

    }

    public function tokens()
    {

    }

    public function logoutAll()
    {

    }
}
