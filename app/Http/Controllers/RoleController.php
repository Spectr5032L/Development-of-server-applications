<?php

namespace App\Http\Controllers;

use App\Http\DTO\ChangeLogsDTO;
use App\Http\DTO\RoleDTO;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function getAll()
    {
        return response()->json(Role::all()->map(function ($role) {
            return ['id' => $role->id, 'role' => RoleDTO::fromModelToDTO($role)];
        }));
    }

    public function getById($id)
    {
        $role = Role::find($id);

        if ($role) {
            return response()->json(['id' => $role->id, 'role' => RoleDTO::fromModelToDTO($role)]);
        }

        return response()->json(['Роль с таким id не найдена'], 404);
    }

    public function create(CreateRoleRequest $request)
    {
        DB::beginTransaction();

        try {
            $role = new Role($request->toDTO()->toArray());
            $role->save();
            $newData = $role->toArray();

            $changeLogsDTO = new ChangeLogsDTO(
                'Roles',
                $role->id,
                json_encode(null),
                json_encode($newData),
                auth()->id()
            );
            ChangeLogsController::create($changeLogsDTO);

            DB::commit();
            return response()->json($role, 201);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка создания роли'], 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $role = Role::find($id);

            if ($role) {
                $oldData = $role->toArray();
                $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value) {
                    return !is_null($value);
                });

                $role->update($dataToUpdate);
                $newData = $role->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Roles',
                    $role->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                DB::commit();
                return response()->json($role);
            }

            return response()->json(['Роль с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка обновления роли'], 500);
        }
    }

    public function hardDelete($id)
    {
        DB::beginTransaction();

        try {
            $role = Role::find($id);

            if ($role) {
                $oldData = $role->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Roles',
                    $role->id,
                    json_encode($oldData),
                    json_encode(null),
                    auth()->id()
                );

                ChangeLogsController::create($changeLogsDTO);
                $role->forceDelete();

                DB::commit();
                return response()->json(['Роль была жёстко удалена']);
            }

            return response()->json(['Роль с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка жёсткого удаления роли'], 500);
        }
    }

    public function softDelete($id)
    {
        DB::beginTransaction();

        try {
            $role = Role::find($id);

            if ($role) {
                $oldData = $role->toArray();
                $role->deleted_by = Auth::id();
                $role->deleted_at = now();
                $role->save();
                $newData = $role->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Roles',
                    $role->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                $role->delete();

                DB::commit();
                return response()->json(['Роль была мягко удалена']);
            }

            return response()->json(['Роль с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка мягкого удаления роли'], 500);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $role = Role::find($id);

            if ($role) {
                return response()->json(['Роль не была удалена']);
            }

            $role = Role::withTrashed()->find($id);

            if ($role) {
                $oldData = $role->toArray();
                $role->restore();
                $role->deleted_by = null;
                $role->deleted_at = null;
                $role->save();
                $newData = $role->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Roles',
                    $role->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                DB::commit();
                return response()->json(['Роль была восстановлена']);
            }

            return response()->json(['Роль с таким id не найдена'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка восстановления роли'], 500);
        }
    }

    public function getStory($id)
    {
        return response()->json(ChangeLogsController::getStory('Roles', $id));
    }

    public function change(Request $request, $id)
    {
        return response()->json(ChangeLogsController::change($id, $request->input('log_id')));
    }
}