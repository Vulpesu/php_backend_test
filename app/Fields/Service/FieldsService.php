<?php
namespace App\Fields\Service;

use App\Fields\Registries\FieldFactory;
use App\Fields\Registries\RegisterFields;

class FieldsService
{
    private array $fields; 

    public function __construct(array $rawFields)
    {
        $fieldCreator = new FieldFactory;
        $fieldRegistrator = new RegisterFields;
        $this->fields = $fieldRegistrator->registrationFields($rawFields, $fieldCreator);
    }

    public function getFields()
    {
        return $this->fields;
    }
}