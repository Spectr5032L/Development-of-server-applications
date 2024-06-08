<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserAndRoleRequest;
use App\Http\Requests\UpdateUserAndRoleRequest;
use App\Models\UsersAndRoles;
use Exception;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAndRoleController extends Controller
{
    public function getAll()
    {
        return response()->json(UsersAndRoles::all());
    }

    public function getById($id)
    {
        $userAndRole = UsersAndRoles::find($id);

        if ($userAndRole) {
            return response()->json($userAndRole);
        }

        return response()->json(['Роль пользователя с таким id не найдена'], 404);
    }

    public function create(CreateUserAndRoleRequest $request)
    {
        DB::beginTransaction();

        try {
            $userAndRole = new UsersAndRoles($request->toDTO()->toArray());
            $userAndRole->save();

            DB::commit();
            return response()->json($userAndRole, 201);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка создания роли пользователя'], 500);
        }
    }

    public function update(UpdateUserAndRoleRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $userAndRole = UsersAndRoles::find($id);

            if ($userAndRole) {
                $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value) {
                    return !is_null($value);
                });

                $userAndRole->update($dataToUpdate);
                DB::commit();
                return response()->json($userAndRole);
            }

            return response()->json(['Роль пользователя с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка обновления роли пользователя'], 500);
        }
    }

    public function hardDelete($id)
    {
        DB::beginTransaction();

        try {
            $userAndRole = UsersAndRoles::find($id);

            if ($userAndRole) {
                $userAndRole->forceDelete();
                DB::commit();
                return response()->json(['Роль пользователя была жёстко удалена']);
            }

            return response()->json(['Роль пользователя с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка жёсткого удаления роли пользователя'], 500);
        }
    }

    public function softDelete($id)
    {
        DB::beginTransaction();

        try {
            $userAndRole = UsersAndRoles::find($id);

            if ($userAndRole) {
                $userAndRole->deleted_by = Auth::id();
                $userAndRole->deleted_at = now();
                $userAndRole->save();
                $userAndRole->delete();
                DB::commit();
                return response()->json(['Роль пользователя была мягко удалена']);
            }

            return response()->json(['Роль пользователя с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка мягкого удаления роли пользователя'], 500);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $userAndRole = UsersAndRoles::find($id);

            if ($userAndRole) {
                return response()->json(['Роль пользователя не была удалена']);
            }

            $userAndRole = UsersAndRoles::withTrashed()->find($id);

            if ($userAndRole) {
                $userAndRole->restore();
                $userAndRole->deleted_by = null;
                $userAndRole->deleted_at = null;
                $userAndRole->save();
                DB::commit();
                return response()->json(['Роль пользователя была восстановлена']);
            }

            return response()->json(['Роль пользователя с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка восстановления роли пользователя'], 500);
        }
    }
}