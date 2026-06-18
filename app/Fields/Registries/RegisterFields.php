<?php
namespace App\Fields\Registries;

use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Contracts\FieldFactoryInterface;
use App\Fields\Exceptions\FieldValidationException;

class RegisterFields implements FieldRegistratatorInterface
{
    private array $fields = [];

    public function registrationFields(array $rawFields, FieldFactoryInterface $field_creator): array {
        $fields = $field_creator->createFieldsFromData($rawFields);

        $names = array_column($rawFields, 'name');
        $counts = array_count_values($names);
        foreach ($counts as $name => $count) {
            if ($count > 1) {
                throw new FieldValidationException("Поле с наименованием '{$name}' не уникально");
            }
        }

        foreach($fields as $field){
            $this->fields[$field->getFieldName()] = $field;
        }
        
        return $this->fields;
    }
}
