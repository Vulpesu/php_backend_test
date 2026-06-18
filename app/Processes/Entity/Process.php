<?php
namespace App\Processes\Entity;

use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Contracts\FieldFactoryInterface;
use App\Processes\Contracts\ProcessInterface;

class Process implements ProcessInterface
{

    private string $name;
    private array $fields;

    // Возможно временное решение Создание процесса можно вынести в отдельный класс где будет сценарии работы с ним, также запись в Базу данных
    public function __construct(string $name, array $fields) {
        $this->setName($name);
        $this->setFields($fields);
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void 
    {
        $this->name = $name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

}
