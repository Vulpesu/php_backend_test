<?php
namespace App\Processes\Contracts;

use App\Processes\Contracts\ProcessInterface;

interface ProcessRepositoryInterface
{

    public function save(ProcessInterface $Process) : ProcessInterface;
    public function findById(int $Id) : ?ProcessInterface;
    public function findByName(string $Name) : ?ProcessInterface;

}
