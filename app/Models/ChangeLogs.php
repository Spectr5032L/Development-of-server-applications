<?php

namespace App\Models;

use App\Http\DTO\PermissionCollectionDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeLogs extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entity',
        'record',
        'old_record',
        'new_record',
        'created_by',
    ];

    protected $casts = [
        'old_record' => 'array',
        'new_record' => 'array',
    ];

    protected $dates = [
        'created_at',
        'deleted_at'
    ];
}