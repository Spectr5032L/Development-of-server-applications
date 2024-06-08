<?php

namespace App\Http\DTO;

class ChangeLogsCollectionDTO
{
    public $changeLogs;

    public function __construct(array $changeLogs)
    {
        $this->changeLogs = $changeLogs;
    }

    public static function fromCollectionToDTO($changeLogsCollection) : self
    {
        $changeLogsDTOs = [];

        foreach ($changeLogsCollection as $changeLogs)
        {
            $changeLogsDTOs[] = ChangeLogsDTO::fromModelToDTO($changeLogs);
        }

        return new self($changeLogsDTOs);
    }
}