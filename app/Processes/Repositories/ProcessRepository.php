<?php
namespace App\Processes\Repositories;

use App\Processes\Contracts\ProcessRepositoryInterface;
use App\Processes\Contracts\ProcessInterface;

class ProcessRepository implements ProcessRepositoryInterface
{

    public  function __construct(private PDO $pdo) {}

    function save(ProcessInterface $Process){
    
        // if ($Process->getId() === null) {
            return $this->insert($user);
        // }

        // return $this->update($user);

    }
    
    function findById(int $Id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM process WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch();

        return $data ? Process::fromArray($data) : null;
    }

    function findByName(string $Name)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM process WHERE name = :name');
        $stmt->execute([':name' => $Name]);

        $data = $stmt->fetch();

        return $data ? Process::fromArray($data) : null;
    }

    private function insert(ProcessInterface $Process): ProcessInterface
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO process (Name)
             VALUES (:name)'
        );

        $stmt->execute([
            ':name'  => $Process->getName(),
        ]);

        // Возвращаем объект с присвоенным ID
        return $this->findById((int) $this->pdo->lastInsertId());
    }
}