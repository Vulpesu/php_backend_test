<?php
namespace App\Processes;

use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Contracts\FieldFactoryInterface;
use App\Processes\Contracts\ProcessInterface;

class Process implements ProcessInterface
{

    private string $Name;
    private array $Fields;

    // Возможно временное решение Создание процесса можно вынести в отдельный класс где будут и спецификации, также запись в Базу данных
    public function __construct(string $Name, array $raw_Fields, FieldRegistratatorInterface $FieldRegistrator, FieldFactoryInterface $FieldCreator) {
        $this->setName($Name);
        $this->addFields($raw_Fields, $FieldRegistrator, $FieldCreator);
    }
    
    public function getName(): string
    {
        return $this->Name;
    }

    public function setName(string $Name): void 
    {
        $this->Name = $Name;
    }

    
    public function getFields(): array
    {
        return $this->Fields;
    }

    public function addFields(array $raw_Fields, FieldRegistratatorInterface $FieldRegistrar, FieldFactoryInterface $Field_creator): void
    {
        $this->Fields = $FieldRegistrar->registrationFields($raw_Fields, $Field_creator);
    }

}
