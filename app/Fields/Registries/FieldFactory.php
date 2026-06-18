<?php
namespace App\Fields\Registries;

use App\Fields\Contracts\FieldFactoryInterface;
use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Types\Text;
use App\Fields\Types\Date;
use App\Fields\Types\Number;
use App\Fields\Exceptions\FieldValidationException;

class FieldFactory implements FieldFactoryInterface {
    
    // Маппинг надо определить динамически за классом, так как может меняться
    private array $map = [
        'Text'   => Text::class,
        'Number' => Number::class,
        'Date'   => Date::class,
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

            if (empty($name)) {
                throw new FieldValidationException('Field name is required');
            }

            if (empty($type)) {
                throw new FieldValidationException('Field type is required');
            }

            if (!isset($this->map[$type])) {
                throw new FieldValidationException("Unknown field type: {$type}");
            }

            // Определяем имя класса из карты маппинга
            $FieldClass = $this->map[$type];

            // Также необходимо продумать валидация наименования, уникально ли наименования поля
            $Fields[] = new $FieldClass($name, $value);
        }

        return $Fields;
    }
}

