<?php
namespace App\Fields\Types;

use App\Fields\Contracts\FieldInterface;
use DateTime;

class Date implements FieldInterface
{
    private string $fieldName;
    private string $fieldValue;
    private string $format;

    public function __construct(
        string $name,
        ?string $value = null,
        string $format = 'Y-m-d H:i:s'
    ) {
        $this->format = $format;

        $this->setFieldName($name);
        $this->setFieldValue($value);
    }

    public function get_DefaultValue(): string
    {
        return (new DateTime())->format($this->format);
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

    public function setFieldValue(?string $fieldValue): void
    {
        $this->fieldValue = $fieldValue ?? $this->get_DefaultValue();
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function asArray(): array
    {
        return [
            'name' => $this->getFieldName(),
            'value' => $this->getFieldValue(),
            'type' => basename(get_class($this)),
            'format' => $this->format
        ];
    }
}