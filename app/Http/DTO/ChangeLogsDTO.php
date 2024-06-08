<?php

namespace App\Http\DTO;

use App\Models\ChangeLogs;

class ChangeLogsDTO
{
    public $entity;
    public $record;
    public $old_record;
    public $new_record;
    public $created_by;

    public function __construct($entity, $record, $old_record, $new_record, $created_by)
    {
        $this->entity = $entity;
        $this->record = $record;
        $this->old_record = $old_record;
        $this->new_record = $new_record;
        $this->created_by = $created_by;
    }

    public function toArray(): array
    {
        return [
            'entity' => $this->entity,
            'record' => $this->record,
            'old_record' => $this->old_record,
            'new_record' => $this->new_record,
            'created_by' => $this->created_by,
        ];
    }
    public static function fromModelToDTO(ChangeLogs $changeLogs): self
    {
        return new self(
            $changeLogs->entity,
            $changeLogs->record,
            $changeLogs->old_record,
            $changeLogs->new_record,
            $changeLogs->created_by,
        );
    }
}