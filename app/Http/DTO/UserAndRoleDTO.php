<?php

namespace App\Http\DTO;

use App\Models\UsersAndRoles;

class UserAndRoleDTO
{
    public $user_id;
    public $role_id;
    public $created_at;
    public $created_by;
    public $deleted_at;
    public $deleted_by;

    public function __construct($user_id, $role_id, $created_at, $created_by, $deleted_at = null, $deleted_by = null)
    {
        $this->user_id = $user_id;
        $this->role_id = $role_id;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
        $this->deleted_at = $deleted_at;
        $this->deleted_by = $deleted_by;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'role_id' => $this->role_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ];
    }
    public static function fromModelToDTO(UsersAndRoles $userAndRole): self
    {
        return new self(
            $userAndRole->user_id,
            $userAndRole->role_id,
            $userAndRole->created_at,
            $userAndRole->created_by,
            $userAndRole->deleted_at,
            $userAndRole->deleted_by
        );
    }
}