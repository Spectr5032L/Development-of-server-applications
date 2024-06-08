<?php

namespace App\Http\Middleware;

use App\Http\DTO\UserDTO;
use Closure;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckPermissions
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $userDTO = UserDTO::fromModelToDTO($user);
        $roles = collect($userDTO->roles);

        $rolesCiphers = $roles->pluck('cipher');
        if ($rolesCiphers->contains('ADMIN'))
        {
            return $next($request);
        }

        $route = Route::getRoutes()->match($request);
        list($controller, $method) = explode('@', class_basename($route->getActionName()));
        $requiredPermission = $this->mapMethodToAction($method) . '-' . $this->mapControllerToDomain($controller);

        $permissions = collect();
        foreach ($roles as $role)
        {
            $permissions = $permissions->merge(collect($role->permissions)->pluck('cipher'));
        }
        $permissions = $permissions->unique();

        if (!$permissions->contains($requiredPermission) ||
            ($rolesCiphers->contains('USER') && $request->route('id') != $user->id
            && collect(['read-user', 'update-user'])->contains($requiredPermission)))
        {
            return response()->json(['Отсутствует разрешение ' . $requiredPermission], 403);
        }
        return $next($request);
    }

    protected function mapControllerToDomain($controller)
    {
        $map = [
            'UserController' => 'user',
            'RoleController' => 'role',
            'PermissionController' => 'permission',
            'UserAndRoleController' => 'userAndRole',
            'RoleAndPermissionController' => 'roleAndPermission',
        ];

        return $map[$controller] ?? null;
    }

    protected function mapMethodToAction($method)
    {
        $map = [
            'getAll' => 'get-list',
            'create' => 'create',
            'getById' => 'read',
            'update' => 'update',
            'hardDelete' => 'delete',
            'softDelete' => 'delete',
            'restore' => 'restore',
            'story' => 'get-story',
            'change' => 'change',
        ];

        return $map[$method] ?? null;
    }
}