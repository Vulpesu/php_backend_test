<?php
namespace App\Fields\Contracts;

interface FieldInterface
{
    public function get_DefaultValue(): string;

    public function getFieldName(): string;
    public function setFieldName(string $FieldName): void;

    public function getFieldValue(): string;
    public function setFieldValue(string $FieldValue): void;

    public function asArray(): array;
}