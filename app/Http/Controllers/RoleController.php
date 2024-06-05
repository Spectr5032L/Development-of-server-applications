<?php

namespace App\Http\Controllers;

use App\Http\DTO\RoleDTO;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

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

        if ($role)
        {
            return response()->json(['id' => $role->id, 'role' => RoleDTO::fromModelToDTO($role)]);
        }

        return response()->json([' (getById) Пользователь с таким id не найден'], 404);
    }

    public function create(CreateRoleRequest $request)
    {
        $role = new Role($request->toDTO()->toArray());
        $role->save();
        return response()->json($role, 201);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::find($id);

        if ($role)
        {
            $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value){
                return !is_null($value);
            });

            $role->update($dataToUpdate);
            return response()->json($role);
        }

        return response()->json([' (update) Роль с таким id не найдена'], 404);
    }

    public function hardDelete($id)
    {
        $role = Role::find($id);

        if ($role)
        {
            $role->forceDelete();
            return response()->json(['Роль была жёстко удалена']);
        }

        return response()->json([' (hardDelete) Роль с таким id не найдена'], 404);
    }

    public function softDelete($id)
    {
        $role = Role::find($id);

        if ($role)
        {
            $role->deleted_by = Auth::id();
            $role->deleted_at = now();
            $role->save();
            $role->delete();
            return response()->json(['Роль была мягко удалена']);
        }

        return response()->json([' (softDelete) Роль с таким id не найдена'], 404);
    }

    public function restore($id)
    {
        $role = Role::find($id);
        if ($role)
        {
            return response()->json(['Роль не была удалена']);
        }

        $role = Role::withTrashed()->find($id);
        if ($role)
        {
            $role->restore();
            $role->deleted_by = null;
            $role->deleted_at = null;
            $role->save();
            return response()->json(['Роль была восстановлена']);
        }
        return response()->json([' (restore) Роль с таким id не найдена'], 404);
    }
}