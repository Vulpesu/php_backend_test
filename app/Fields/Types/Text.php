<?php
namespace App\Fields\Types;

use App\Fields\Contracts\FieldInterface;

class Text implements FieldInterface
{

    private string $FieldName;
    private string $FieldValue;
    
    public function __construct(string $name, string $value) {
        $this->setFieldName($name);
        $this->setFieldValue($value);
    }

    
    public function get_DefaultValue(): string
    {
        return "";
    }

    
    public function getFieldName(): string
    {
        return $this->FieldName;
    }

    
    public function setFieldName(string $FieldName): void
    {
        $this->FieldName = $FieldName;
    }

    
    public function getFieldValue(): string
    {
        return $this->FieldValue;
    }

    
    public function setFieldValue(string $FieldValue): void
    {
        $this->FieldValue = $FieldValue ?? $this->get_DefaultValue();
    }
}