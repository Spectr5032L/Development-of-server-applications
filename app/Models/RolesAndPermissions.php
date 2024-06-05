<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolesAndPermissions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
        'permission_id',
        'created_by',
        'deleted_by'
    ];

    protected $dates = [
        'created_at',
        'deleted_at'
    ];
}