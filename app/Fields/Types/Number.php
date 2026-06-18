<?php
namespace App\Fields\Types;

use App\Fields\Contracts\FieldInterface;

class Number implements FieldInterface
{

    private string $fieldName;
    private string $fieldValue;

    public function __construct(string $name, string $value) {
        $this->setFieldName($name);
        $this->setFieldValue($value);
    }
    
    public function get_DefaultValue(): string
    {
        return 0;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    
    public function setFieldName(string $FieldName): void
    {
        $this->fieldName = $FieldName;
    }

    
    public function getFieldValue(): string
    {
        return $this->fieldValue;
    }

    
    public function setFieldValue(string $fieldValue): void
    {
        $this->fieldValue = $fieldValue ?? $this->get_DefaultValue();
    }

    public function asArray(): array
    {
        return [
                'name' => $this->getFieldName(),
                'value' => $this->getFieldValue(),
                'type' => basename(get_class($this))
            ];
    }
}
