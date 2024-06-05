<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserAndRoleRequest;
use App\Http\Requests\UpdateUserAndRoleRequest;
use App\Models\UsersAndRoles;
use Illuminate\Support\Facades\Auth;

class UserAndRoleController extends Controller
{
    public function getAll()
    {
        return response()->json(UsersAndRoles::all());
    }

    public function getById($id)
    {
        $userAndRole = UsersAndRoles::find($id);

        if ($userAndRole)
        {
            return response()->json($userAndRole);
        }

        return response()->json([' (getById) Роль пользователя с таким id не найдена'], 404);
    }

    public function create(CreateUserAndRoleRequest $request)
    {
        $userAndRole = new UsersAndRoles($request->toDTO()->toArray());
        $userAndRole->save();
    }

    public function update(UpdateUserAndRoleRequest $request, $id)
    {
        $userAndRole = UsersAndRoles::find($id);

        if ($userAndRole)
        {
            $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value){
                return !is_null($value);
            });

            $userAndRole->update($dataToUpdate);
            return response()->json($userAndRole);
        }

        return response()->json(['(update) Роль пользователя с таким id не найдена'], 404);
    }

    public function hardDelete($id)
    {
        $userAndRole = UsersAndRoles::find($id);

        if ($userAndRole)
        {
            $userAndRole->forceDelete();
            return response()->json(['Роль пользователя была жёстко удалена']);
        }

        return response()->json([' (hardDelete) Роль пользователя с таким id не найдена'], 404);
    }

    public function softDelete($id)
    {
        $userAndRole = UsersAndRoles::find($id);

        if ($userAndRole)
        {
            $userAndRole->deleted_by = Auth::id();
            $userAndRole->deleted_at = now();
            $userAndRole->save();
            $userAndRole->delete();
            return response()->json(['Роль пользователя была мягко удалена']);
        }

        return response()->json([' (softDelete) Роль пользователя с таким id не найдена'], 404);
    }

    public function restore($id)
    {
        $userAndRole = UsersAndRoles::find($id);
        if ($userAndRole)
        {
            return response()->json(['Роль пользователя не была удалена']);
        }

        $userAndRole = UsersAndRoles::withTrashed()->find($id);
        if ($userAndRole)
        {
            $userAndRole->restore();
            $userAndRole->deleted_by = null;
            $userAndRole->deleted_at = null;
            $userAndRole->save();
            return response()->json(['Роль пользователя была восстановлена']);
        }
        return response()->json([' (restore) Роль пользователя с таким id не найдена'], 404);
    }
}