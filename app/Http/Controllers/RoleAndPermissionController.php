<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleAndPermissionRequest;
use App\Http\Requests\UpdateRoleAndPermissionRequest;
use App\Models\RolesAndPermissions;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleAndPermissionController extends Controller
{
    public function getAll()
    {
        return response()->json(RolesAndPermissions::all());
    }

    public function getById($id)
    {
        $roleAndPermission = RolesAndPermissions::find($id);

        if ($roleAndPermission) {
            return response()->json($roleAndPermission);
        }

        return response()->json(['Разрешение роли с таким id не найдено'], 404);
    }

    public function create(CreateRoleAndPermissionRequest $request)
    {
        DB::beginTransaction();

        try {
            $roleAndPermission = new RolesAndPermissions($request->toDTO()->toArray());
            $roleAndPermission->save();
            DB::commit();
            return response()->json($roleAndPermission, 201);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка создания разрешения роли'], 500);
        }
    }

    public function update(UpdateRoleAndPermissionRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $roleAndPermission = RolesAndPermissions::find($id);

            if ($roleAndPermission) {
                $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value) {
                    return !is_null($value);
                });

                $roleAndPermission->update($dataToUpdate);
                DB::commit();
                return response()->json($roleAndPermission);
            }

            return response()->json(['Разрешение роли с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка обновления разрешения роли'], 500);
        }
    }

    public function hardDelete($id)
    {
        DB::beginTransaction();

        try {
            $roleAndPermission = RolesAndPermissions::find($id);

            if ($roleAndPermission) {
                $roleAndPermission->forceDelete();
                DB::commit();
                return response()->json(['Разрешение роли было жёстко удалено']);
            }

            return response()->json(['Разрешение роли с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка жёсткого удаления разрешения роли'], 500);
        }
    }

    public function softDelete($id)
    {
        DB::beginTransaction();

        try {
            $roleAndPermission = RolesAndPermissions::find($id);

            if ($roleAndPermission) {
                $roleAndPermission->deleted_by = Auth::id();
                $roleAndPermission->deleted_at = now();
                $roleAndPermission->save();
                $roleAndPermission->delete();
                DB::commit();
                return response()->json(['Разрешение роли было мягко удалено']);
            }

            return response()->json(['Разрешение роли с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка мягкого удаления разрешения роли'], 500);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $roleAndPermission = RolesAndPermissions::find($id);

            if ($roleAndPermission) {
                return response()->json(['Разрешение роли не было удалено']);
            }

            $roleAndPermission = RolesAndPermissions::withTrashed()->find($id);

            if ($roleAndPermission) {
                $roleAndPermission->restore();
                $roleAndPermission->deleted_by = null;
                $roleAndPermission->deleted_at = null;
                $roleAndPermission->save();
                DB::commit();
                return response()->json(['Разрешение роли было восстановлено']);
            }

            return response()->json(['Разрешение роли с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка восстановления разрешения роли'], 500);
        }
    }
}
