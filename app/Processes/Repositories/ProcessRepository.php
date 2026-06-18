<?php
namespace App\Processes\Repositories;

use App\Processes\Contracts\ProcessRepositoryInterface;
use App\Processes\Contracts\ProcessInterface;
use App\Fields\Registries\FieldFactory;
use App\Fields\Registries\RegisterFields;
use App\Processes\Entity\Process;
use App\Processes\Service\ProcessService;
use PDO;
use PDOException;
use RuntimeException;

class ProcessRepository implements ProcessRepositoryInterface
{

    public  function __construct(private PDO $pdo) {}

    public function save(ProcessInterface $Process): void{
    
        if ($this->findByName($Process->getName()) === null) {
            $this->insert($Process);
        }

        $this->update($Process);
    }
    
    public function findById(int $id): ?ProcessInterface
    {
        $stmt = $this->pdo->prepare('SELECT name FROM process WHERE process_id = :process_id');
        $stmt->execute(['process_id' => $id]);
        $process = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$process) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT json FROM fields 
                                        WHERE process_id = :process_id');
        $stmt->execute([':process_id' => $id]);
        $fields = $stmt->fetch(PDO::FETCH_ASSOC);

        $fieldsData = [];

        if ($fields && !empty($fields['json'])) {
            $decoded = json_decode($fields['json'], true);
            
            if (is_array($decoded)) {
                    $fieldsData = $decoded;
            }
        }

        return ProcessService::fromData($process['name'], $fieldsData);
    }

    public function findByName(string $name): ?ProcessInterface
    {

        $stmt = $this->pdo->prepare('SELECT process_id FROM process WHERE name = :name');
        $stmt->execute([':name' => $name]);
        $process = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$process) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT json FROM fields 
                                        WHERE process_id = :process_id');
        $stmt->execute([':process_id' => $process['process_id']]);
        $fields = $stmt->fetch(PDO::FETCH_ASSOC);

        $fieldsData = [];

        if ($fields && !empty($fields['json'])) {
            $decoded = json_decode($fields['json'], true);
            
            if (is_array($decoded)) {
                    $fieldsData = $decoded;
            }
        }


        return ProcessService::fromData($process['name'], $fieldsData);
    }

    public function getFields(ProcessInterface $Process)
    {
        $stmt = $this->pdo->prepare('SELECT json FROM fields 
                                        WHERE process_id = (select process_id from process where name = :name)');
        $stmt->execute([':name' => $Process->getName()]);

        $fields = $stmt->fetch(PDO::FETCH_ASSOC);

        $fieldsData = [];

        if ($fields && !empty($fields['json'])) {
            $decoded = json_decode($fields['json'], true);
            
            if (is_array($decoded)) {
                    $fieldsData = $decoded;
            }
        }

        return $fieldsData;
    }

    private function insert(ProcessInterface $Process): void
    {
        

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO process (Name) VALUES (:name)'
            );

            $stmt->execute([
                ':name' => $Process->getName(),
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException('Процесс с таким именем уже существует');
            }

            throw $e;
        }


        $processId = (int) $this->pdo->lastInsertId();

        $fieldsArray = ProcessService::getFieldsAsArray($Process);
        $jsonString = json_encode($fieldsArray, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $stmt = $this->pdo->prepare('
            INSERT INTO fields (json, process_id)
            VALUES (:json, :process_id)
        ');

        $stmt->execute([
            ':process_id'  => $processId,
            ':json' => $jsonString
        ]);

        //return $this->findById($processId);
    }

    private function update(ProcessInterface $Process): void
    {
        $stmt = $this->pdo->prepare('SELECT process_id FROM process WHERE name = :name');
        $stmt->execute([':name' => $Process->getName()]);
        $process = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$process) {
            throw new RuntimeException('Process not found for update');
        }

        $fieldsArray = ProcessService::getFieldsAsArray($Process);
        $jsonString = json_encode($fieldsArray, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        // $stmt = $this->pdo->prepare('
        //     INSERT INTO fields (process_id, json) 
        //     VALUES (:process_id, :json)
        //     ON DUPLICATE KEY UPDATE json = :json_update
        // ');
        
        $stmt = $this->pdo->prepare('
            INSERT INTO fields (process_id, json)
            VALUES (:process_id, :json)
            ON CONFLICT(process_id) DO UPDATE SET json = excluded.json
        ');

        $stmt->execute([
            ':process_id'  => $process['process_id'],
            ':json'        => $jsonString
        ]);

        //return $this->findById($process['process_id']);
    }

}