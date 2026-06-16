<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Processes\Process;
use App\Fields\Registries\RegisterFields;
use App\Fields\Registries\FieldFactory;


// 4. Тестовые сырые данные
$rawPayload = [
    ['type' => 'text', 'name' => 'username', 'value' => 'Alex']
];

// 5. Создаем объекты. Обратите внимание: БЕЗ ведущего слэша, 
// так как мы импортировали эти классы через "use" в самом начале файла.
$FieldCreator = new FieldFactory();
$FieldRegistrator = new RegisterFields();
$process = new Process("Test", $rawPayload, $FieldRegistrator, $FieldCreator);

echo $process->getFields()['username']->getFieldValue();

