<?php
namespace App\Fields\Registries;

use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Contracts\FieldFactoryInterface;


class RegisterFields implements FieldRegistratatorInterface
{
    private array $Fields = [];

    public function registrationFields(array $raw_Fields, FieldFactoryInterface $Field_creator): array {
        $Fields = $Field_creator->createFieldsFromData($raw_Fields);

        foreach($Fields as $Field){
            $this->Fields[$Field->getFieldName()] = $Field;
        }

        return $this->Fields;
    }
}
