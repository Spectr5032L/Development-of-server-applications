<?php

namespace App\Http\Controllers;

use App\Http\DTO\ChangeLogsDTO;
use App\Http\DTO\UserDTO;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UsersAndRoles;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        return response()->json(['Пользователь с таким id не найден'], 404);
    }

    public function create(CreateUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = new User($request->toDTO()->toArray());
            $user->save();
            $newData = $user->toArray();

            $changeLogsDTO = new ChangeLogsDTO(
                'Users',
                $user->id,
                json_encode(null),
                json_encode($newData),
                auth()->id()
            );
            ChangeLogsController::create($changeLogsDTO);

            $userAndRole = new UsersAndRoles();
            $userAndRole->user_id = $user->id;
            $userAndRole->role_id = Role::where('cipher', 'GUEST')->value('id');
            $userAndRole->created_by = Auth::id();
            $userAndRole->save();

            DB::commit();
            return response()->json(UserDTO::fromModelToDTO($user), 201);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка создания пользователя'], 500);
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);

            if ($user) {
                $oldData = $user->toArray();

                $dataToUpdate = array_filter($request->toDTO()->toArray(), function ($value) {
                    return !is_null($value);
                });

                $user->update($dataToUpdate);
                $newData = $user->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Users',
                    $user->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                DB::commit();
                return response()->json($user);
            }

            return response()->json(['Пользователь с таким id не найден'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка обновления пользователя'], 500);
        }
    }

    public function hardDelete($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);

            if ($user) {
                $oldData = $user->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Users',
                    $user->id,
                    json_encode($oldData),
                    json_encode(null),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                $user->forceDelete();
                DB::commit();
                return response()->json(['Пользователь был жёстко удалён']);
            }

            return response()->json(['Пользователь с таким id не найден'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка жёсткого удаления пользователя'], 500);
        }
    }

    public function softDelete($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);

            if ($user) {
                $oldData = $user->toArray();

                $user->deleted_by = Auth::id();
                $user->deleted_at = now();
                $user->save();
                $newData = $user->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Users',
                    $user->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                $user->delete();
                DB::commit();
                return response()->json(['Пользователь был мягко удалён']);
            }

            return response()->json(['Пользователь с таким id не найден'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка мягкого удаления пользователя'], 500);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);

            if ($user) {
                return response()->json(['Пользователь не был удалён']);
            }

            $user = User::withTrashed()->find($id);

            if ($user) {
                $oldData = $user->toArray();

                $user->restore();
                $user->deleted_by = null;
                $user->deleted_at = null;
                $user->save();
                $newData = $user->toArray();

                $changeLogsDTO = new ChangeLogsDTO(
                    'Users',
                    $user->id,
                    json_encode($oldData),
                    json_encode($newData),
                    auth()->id()
                );
                ChangeLogsController::create($changeLogsDTO);

                DB::commit();
                return response()->json(['Пользователь был восстановлен']);
            }

            return response()->json(['Пользователь с таким id не найден'], 404);
        } catch (Exception $e) {
            DB::rollback();
            
            $errorMessage = $e->getMessage();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
        
            Log::error("Ошибка в файле $errorFile на строке $errorLine: $errorMessage");

            return response()->json(['error' => 'Ошибка восстановления пользователя'], 500);
        }
    }

    public function getStory($id)
    {
        return response()->json(ChangeLogsController::getStory('Users', $id));
    }

    public function change(Request $request, $id)
    {
        return response()->json(ChangeLogsController::change($id, $request->input('log_id')));
    }
}