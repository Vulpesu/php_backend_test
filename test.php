<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Processes\Process;
use App\Fields\Registries\RegisterFields;
use App\Fields\Registries\FieldFactory;
use App\Database\DatabaseConfig;
use App\Database\Connection;
use App\Processes\Repositories\ProcessRepository;


// 4. Тестовые сырые данные
$rawPayload = [
    ['type' => 'text', 'name' => 'username', 'value' => 'Alex']
];

$FieldCreator = new FieldFactory();
$FieldRegistrator = new RegisterFields();
$process = new Process("Test", $rawPayload, $FieldRegistrator, $FieldCreator);

//echo $process->getFields()['username']->getFieldValue();


$databaseconfig = new DatabaseConfig('localhost', 'test_process', 'root', 'SnPAcR|M12!@');
$connection = Connection::get($databaseconfig);

$processRep = new ProcessRepository($connection);

$processRep->save($process);
