<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function getAll()
    {
        return response()->json(Permission::all());
    }

    public function getById($id)
    {
        $permission = Permission::find($id);

        if ($permission)
        {
            return response()->json($permission);
        }

        return response()->json([' (getById) Разрешение с таким id не найдено'], 404);
    }

    public function create(CreatePermissionRequest $request)
    {
        $permission = new Permission($request->toDTO()->toArray());
        $permission->save();
        return response()->json($permission, 201);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = Permission::find($id);

        if ($permission)
        {
            $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value){
                return !is_null($value);
            });

            $permission->update($dataToUpdate);
            return response()->json($permission);
        }

        return response()->json([' (update) Разрешение с таким id не найдено'], 404);
    }

    public function hardDelete($id)
    {
        $permission = Permission::find($id);

        if ($permission)
        {
            $permission->forceDelete();
            return response()->json(['Разрешение было жёстко удалено']);
        }

        return response()->json([' (hardDelete) Разрешение с таким id не найдено'], 404);
    }

    public function softDelete($id)
    {
        $permission = Permission::find($id);

        if ($permission)
        {
            $permission->deleted_by = Auth::id();
            $permission->deleted_at = now();
            $permission->save();
            $permission->delete();
            return response()->json(['Разрешение было мягко удалено']);
        }

        return response()->json([' (softDelete) Разрешение с таким id не найдено'], 404);
    }

    public function restore($id)
    {
        $permission = Permission::find($id);
        if ($permission)
        {
            return response()->json(['Разрешение не было удалено']);
        }

        $permission = Permission::withTrashed()->find($id);
        if ($permission)
        {
            $permission->restore();
            $permission->deleted_by = null;
            $permission->deleted_at = null;
            $permission->save();
            return response()->json(['Разрешение было восстановлено']);
        }
        return response()->json([' (restore) Разрешение с таким id не найдено'], 404);
    }
}