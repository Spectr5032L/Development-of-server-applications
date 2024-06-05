<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleAndPermissionRequest;
use App\Http\Requests\UpdateRoleAndPermissionRequest;
use App\Models\RolesAndPermissions;
use Illuminate\Support\Facades\Auth;

class RoleAndPermissionController extends Controller
{
    public function getAll()
    {
        return response()->json(RolesAndPermissions::all());
    }

    public function getById($id)
    {
        $roleAndPermission = RolesAndPermissions::find($id);

        if ($roleAndPermission)
        {
            return response()->json($roleAndPermission);
        }

        return response()->json([' (getById) Разрешение роли с таким id не найдено'], 404);
    }

    public function create(CreateRoleAndPermissionRequest $request)
    {
        $roleAndPermission = new RolesAndPermissions($request->toDTO()->toArray());
        $roleAndPermission->save();
        return response()->json($roleAndPermission, 201);
    }

    public function update(UpdateRoleAndPermissionRequest $request, $id)
    {
        $roleAndPermission = RolesAndPermissions::find($id);

        if ($roleAndPermission)
        {
            $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value){
                return !is_null($value);
            });

            $roleAndPermission->update($dataToUpdate);
            return response()->json($roleAndPermission);
        }

        return response()->json([' (update) Разрешение роли с таким id не найдено'], 404);
    }

    public function hardDelete($id)
    {
        $roleAndPermission = RolesAndPermissions::find($id);

        if ($roleAndPermission)
        {
            $roleAndPermission->forceDelete();
            return response()->json(['Разрешение роли было жёстко удалено']);
        }

        return response()->json([' (hardDelete) Разрешение роли с таким id не найдено'], 404);
    }

    public function softDelete($id)
    {
        $roleAndPermission = RolesAndPermissions::find($id);

        if ($roleAndPermission)
        {
            $roleAndPermission->deleted_by = Auth::id();
            $roleAndPermission->deleted_at = now();
            $roleAndPermission->save();
            $roleAndPermission->delete();
            return response()->json(['Разрешение роли было мягко удалено']);
        }

        return response()->json([' (softDelete) Разрешение роли с таким id не найдено'], 404);
    }

    public function restore($id)
    {
        $roleAndPermission = RolesAndPermissions::find($id);
        if ($roleAndPermission)
        {
            return response()->json(['Разрешение роли не было удалено']);
        }

        $roleAndPermission = RolesAndPermissions::withTrashed()->find($id);
        if ($roleAndPermission)
        {
            $roleAndPermission->restore();
            $roleAndPermission->deleted_by = null;
            $roleAndPermission->deleted_at = null;
            $roleAndPermission->save();
            return response()->json(['Разрешение роли было восстановлено']);
        }
        return response()->json([' (restore) Разрешение роли с таким id не найдено'], 404);
    }
}