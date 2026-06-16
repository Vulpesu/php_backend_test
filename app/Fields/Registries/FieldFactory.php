<?php
namespace App\Fields\Registries;

use App\Fields\Contracts\FieldFactoryInterface;
use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Types\Text;
use App\Fields\Types\Date;
use App\Fields\Types\Number;

class FieldFactory implements FieldFactoryInterface {
    
    // Маппинг надо определить динамически за классом, так как может меняться
    private array $map = [
        'text'   => Text::class,
        'number' => Number::class,
        'date'   => Date::class,
    ];

    public function createFieldsFromData(array $rawFields): array {
        $Fields = [];

        foreach ($rawFields as $FieldData) {
            $name = $FieldData['name'] ?? null;
            $type = $FieldData['type'] ?? null;
            $value = $FieldData['value'] ?? null;

            // Временная заглушка в случае если пустые данные(необходимо их отлавливать и выкидывать исключения)
            if (!$name || !$type || !isset($this->map[$type])) {
                continue; 
            }

            // Определяем имя класса из карты маппинга
            $FieldClass = $this->map[$type];

            // Также необходимо продумать валидация наименования, уникально ли наименования поля
            $Fields[] = new $FieldClass($name, $value);
        }

        return $Fields;
    }
}

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
