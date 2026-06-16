<?php
namespace App\Fields\Contracts;

interface FieldRepositoryInterface
{

    public function save(FieldInterface $Field) : int;
    public function findById(int $Id) : FieldInterface;
    public function findByName(string $Name) : FieldInterface;

}