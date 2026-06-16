<?php
namespace App\Fields\Contracts;

use App\Fields\Contracts\FieldFactoryInterface;

interface FieldRegistratatorInterface
{
    public function registrationFields(array $raw_Fields, FieldFactoryInterface $Field_creator): array;
}