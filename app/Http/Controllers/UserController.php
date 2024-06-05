<?php

namespace App\Http\Controllers;

use App\Http\DTO\UserDTO;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UsersAndRoles;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getAll()
    {
        return response()->json(User::all()->map(function ($user) {
            return ['id' => $user->id, 'user' => UserDTO::fromModelToDTO($user)];
        }));
    }

    public function getById($id)
    {
        $user = User::find($id);

        if ($user)
        {
            return response()->json(['id' => $user->id, 'user' => UserDTO::fromModelToDTO($user)]);
        }

        return response()->json([' (getById) Пользователь с таким id не найден'], 404);
    }

        public function create(CreateUserRequest $request)
    {
        $user = new User($request->toDTO()->toArray());
        $user->save();
        $userAndRole = new UsersAndRoles();
        $userAndRole->user_id = $user->id;
        $userAndRole->role_id = Role::where('cipher', 'GUEST')->value('id');
        $userAndRole->created_by = Auth::id();
        $userAndRole->save();
        return response()->json(UserDTO::fromModelToDTO($user), 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if ($user)
        {
            $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value){
                return !is_null($value);
            });

            $user->update($dataToUpdate);
            return response()->json($user);
        }

        return response()->json([' (update) Пользователь с таким id не найден'], 404);
    }

    public function hardDelete($id)
    {
        $user = User::find($id);

        if ($user)
        {
            $user->forceDelete();
            return response()->json(['Пользователь был жёстко удалён']);
        }

        return response()->json([' (hardDelete) Пользователь с таким id не найден'], 404);
    }

    public function softDelete($id)
    {
        $user = User::find($id);

        if ($user)
        {
            $user->deleted_by = Auth::id();
            $user->deleted_at = now();
            $user->save();
            $user->delete();
            return response()->json(['Пользователь был мягко удалён']);
        }

        return response()->json([' (softDelete) Пользователь с таким id не найден'], 404);
    }

    public function restore($id)
    {
        $user = User::find($id);
        if ($user)
        {
            return response()->json(['Пользователь не был удалён']);
        }

        $user = User::withTrashed()->find($id);
        if ($user)
        {
            $user->restore();
            $user->deleted_by = null;
            $user->deleted_at = null;
            $user->save();
            return response()->json(['Пользователь был восстановлен']);
        }
        return response()->json([' (restore) Пользователь с таким id не найден'], 404);
    }
}