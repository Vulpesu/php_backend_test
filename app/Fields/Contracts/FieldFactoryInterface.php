<?php
namespace App\Fields\Contracts;

interface FieldFactoryInterface
{
    public function createFieldsFromData(array $rawFields): array;
}