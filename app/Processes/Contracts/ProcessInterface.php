<?php
namespace App\Processes\Contracts;

use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Contracts\FieldFactoryInterface;

interface ProcessInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getFields(): array;

    public function setFields(array $fields): void;

}