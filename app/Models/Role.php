<?php

namespace App\Models;

use App\Http\DTO\PermissionCollectionDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'cipher',
        'created_by',
        'deleted_by'
    ];

    protected $dates = [
        'created_at',
        'deleted_at'
    ];

    public function permissions()
    {
        return PermissionCollectionDTO::fromCollectionToDTO($this->belongsToMany(Permission::class,
            'roles_and_permissions', 'role_id', 'permission_id')->get());
    }
}