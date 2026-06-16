<?php
namespace App\Processes\Contracts;

use App\Fields\Contracts\FieldRegistratatorInterface;
use App\Fields\Contracts\FieldFactoryInterface;

interface ProcessInterface
{

    public function getName(): string;

    public function setName(string $Name): void;

    public function getFields(): array;

    public function addFields(array $raw_Fields, FieldRegistratatorInterface $FieldRegistrar, FieldFactoryInterface $FieldCreator): void;

}