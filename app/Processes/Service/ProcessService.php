<?php
namespace App\Processes\Service;

use PDO;
use App\Processes\Contracts\ProcessInterface;
use App\Processes\DTO\ProcessDTO;
use App\Processes\Entity\Process;
use App\Fields\Service\FieldsService;
use App\Processes\Repositories\ProcessRepository;

class ProcessService
{

    private ProcessInterface $Process;

    public function __construct(ProcessDTO $DTO, PDO $connection)
    {
        $this->Process = $this->ProcessCreator($DTO->fields, $DTO->name);
        $processRep = new ProcessRepository($connection);
        $processRep->save($this->Process);
    }
    
    // FieldService надо вытащить через интерфейс
    public function ProcessCreator(array $fieldsData, string $name): ProcessInterface
    {
        $fieldService = new FieldsService($fieldsData);
        return new Process($name, $fieldService->getFields());
    }

    public static function getFieldsAsArray(ProcessInterface $process): array
    {
        $rawPayload = [];

        foreach ($process->getFields() as $fieldObject) {
            $rawPayload[] = $fieldObject->asArray();
        }

        return $rawPayload;
    }
    
    // FieldService надо вытащить через интерфейс
    public static function fromData(string $name, array $rawPayload): ProcessInterface
    {
        $fieldService = new FieldsService($rawPayload);
        $fields = $fieldService->getFields();
        return new Process($name, $fields);
    }
}