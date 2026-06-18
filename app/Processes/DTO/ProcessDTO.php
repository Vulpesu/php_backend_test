<?php
namespace App\Processes\DTO;

class ProcessDTO
{

    public readonly string $name;
    public readonly array $fields;

    public function __construct(string $name, array $fields)
    {
        $this->name = $name;
        $this->fields = $fields;
    }
}