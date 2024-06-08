<?php

namespace App\Http\Controllers;

use App\Http\DTO\ChangeLogsDTO;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function getAll()
    {
        return response()->json(Permission::all());
    }

    public function getById($id)
    {
        $permission = Permission::find($id);

        if ($permission) {
            return response()->json($permission);
        }

        return response()->json([' (getById) Разрешение с таким id не найдено'], 404);
    }

    public function create(CreatePermissionRequest $request)
    {
        DB::beginTransaction();

        try {
            $permission = new Permission($request->toDTO()->toArray());
            $permission->save();
            $newData = $permission->toArray();

            $changeLogsDTO = new ChangeLogsDTO(
                'Permissions',
                $permission->id,
                json_encode(null),
                json_encode($newData),
                auth()->id()
            );
            ChangeLogsController::create($changeLogsDTO);

            DB::commit();
            return response()->json($permission, 201);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка создания разрешения'], 500);
        }
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::find($id);

            if ($permission) {
                $oldData = $permission->toArray();

                $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value) {
                    return !is_null($value);
                });

                $permission->update($dataToUpdate);
                $newData = $permission->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Permissions',
                    $permission->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                DB::commit();
                return response()->json($permission);
            }

            return response()->json([' (update) Разрешение с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка обновления разрешения'], 500);
        }
    }

    public function hardDelete($id)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::find($id);

            if ($permission) {
                $oldData = $permission->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Permissions',
                    $permission->id,
                    json_encode($oldData),
                    json_encode(null),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                $permission->forceDelete();
                DB::commit();
                return response()->json(['Разрешение было жёстко удалено']);
            }

            return response()->json([' (hardDelete) Разрешение с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка жесткого удаления разрешения'], 500);
        }
    }

    public function softDelete($id)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::find($id);

            if ($permission) {
                $oldData = $permission->toArray();

                $permission->deleted_by = Auth::id();
                $permission->deleted_at = now();
                $permission->save();
                $newData = $permission->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Permissions',
                    $permission->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                $permission->delete();
                DB::commit();
                return response()->json(['Разрешение было мягко удалено']);
            }

            return response()->json([' (softDelete) Разрешение с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка мягкого удаления разрешения'], 500);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::find($id);

            if ($permission) {
                return response()->json(['Разрешение не было удалено']);
            }

            $permission = Permission::withTrashed()->find($id);

            if ($permission) {
                $oldData = $permission->toArray();

                $permission->restore();
                $permission->deleted_by = null;
                $permission->deleted_at = null;
                $permission->save();
                $newData = $permission->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Permissions',
                    $permission->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                DB::commit();
                return response()->json(['Разрешение было восстановлено']);
            }

            return response()->json([' (restore) Разрешение с таким id не найдено'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка восстановления разрешения'], 500);
        }
    }

    public function getStory($id)
    {
        return response()->json(ChangeLogsController::getStory('Permissions', $id));
    }

    public function change(Request $request, $id)
    {
        return response()->json(ChangeLogsController::change($id, $request->input('log_id')));
    }
}