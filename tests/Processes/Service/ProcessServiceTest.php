<?php
namespace Tests\ProcessService;

use PDO;
use PDOStatement;
use PDOException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Processes\Service\ProcessService;
use App\Processes\DTO\ProcessDTO;
use App\Processes\Contracts\ProcessInterface;



/**
 * @covers \App\Processes\Service\ProcessService
 *
 * ВНИМАНИЕ: конструктор ProcessService нарушает SRP и принцип DI:
 *  - создаёт зависимости через new внутри методов (FieldsService, ProcessRepository)
 *  - выполняет бизнес-логику и запись в БД прямо в __construct
 *  - ProcessCreator() возвращает результат, но конструктор его игнорирует
 *    (присвоения $this->Process не происходит — баг)
 *
 * Тесты конструктора поэтому интеграционные (реальный PDO/SQLite).
 * Статические методы тестируются как чистые unit-тесты.
 */
class ProcessServiceTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Вспомогательные методы
    // -------------------------------------------------------------------------

    /**
     * SQLite-соединение в памяти с нужной схемой.
     * Используется только для тестов конструктора, где ProcessRepository
     * создаётся внутри через new — мок PDO не подойдёт.
     */
    private function makeInMemoryPdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Схема должна совпадать с тем, что ожидает ProcessRepository::save()
        // Замените имена столбцов под реальную реализацию
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS process (
                process_id    INTEGER PRIMARY KEY AUTOINCREMENT,
                name  TEXT    NOT NULL
            )
        ');

        $pdo->exec('
            CREATE TABLE fields (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                process_id INTEGER NOT NULL UNIQUE,
                json TEXT,
                FOREIGN KEY (process_id) REFERENCES process(process_id)
            );');

        return $pdo;
    }

    /**
     * Минимальный мок ProcessInterface с заданными полями.
     *
     * @param  array<\App\Fields\Contracts\FieldRegistratorInterface> $fields
     */
    private function makeProcessMock(array $fields = []): MockObject&ProcessInterface
    {
        $mock = $this->createMock(ProcessInterface::class);
        
        $mock->method('getFields')
         ->willReturn($fields);

        $mock->fields = $fields;
        return $mock;
    }

    /**
     * Мок одного поля, чей asArray() возвращает $data.
     */
    private function makeFieldMock(array $data): object
    {
        $field = new class($data) {
            public function __construct(private array $data) {}
            public function asArray(): array { return $this->data; }
        };

        return $field;
    }

    // -------------------------------------------------------------------------
    // Тесты конструктора
    // -------------------------------------------------------------------------

    /**
     * Конструктор должен успешно создать сервис и сохранить процесс.
     * Проверяем через запись в БД (интеграционный тест).
     */
    public function test_constructor_saves_process_to_repository(): void
    {
        $pdo = $this->makeInMemoryPdo();

        $dto = new ProcessDTO('Тестовый процесс', [
                                                    ['type' => 'text',   'name' => 'Имя',    'value' => 'Иван'],
                                                    ['type' => 'number', 'name' => 'Возраст', 'value' => 30],
                                                ]);

        new ProcessService($dto, $pdo);

        $row = $pdo->query("SELECT * FROM process WHERE name = 'Тестовый процесс'")->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($row, 'Процесс должен быть сохранён в БД');
        $this->assertSame('Тестовый процесс', $row['name']);
    }

    /**
     * Конструктор с пустым списком полей не должен бросать исключение.
     */
    public function test_constructor_accepts_empty_fields(): void
    {
        $pdo = $this->makeInMemoryPdo();

        $dto = new ProcessDTO('dummy', []);
        $this->expectNotToPerformAssertions();
        new ProcessService($dto, $pdo);
    }

    // -------------------------------------------------------------------------
    // ProcessCreator
    // -------------------------------------------------------------------------

    /**
     * ProcessCreator должен возвращать ProcessInterface.
     */
    public function test_ProcessCreator_returns_process_interface(): void
    {
        $pdo = $this->makeInMemoryPdo();

        
        $dto = new ProcessDTO('dummy', []);
        $service = new ProcessService($dto, $pdo);

        $result = $service->ProcessCreator(
            [['type' => 'text', 'name' => 'Поле', 'value' => 'значение']],
            'Новый процесс'
        );

        $this->assertInstanceOf(ProcessInterface::class, $result);
    }

    /**
     * ProcessCreator должен передавать имя в создаваемый Process.
     */
    public function test_ProcessCreator_passes_name_to_process(): void
    {
        $pdo = $this->makeInMemoryPdo();

        $dto = new ProcessDTO('dummy', []);

        $service = new ProcessService($dto, $pdo);
        $process = $service->ProcessCreator([], 'МойПроцесс');

        // Process::getName() или публичное свойство — адаптируйте под реальный API
        $this->assertSame('МойПроцесс', $process->getName());
    }

    // -------------------------------------------------------------------------
    // getFieldsAsArray (статический)
    // -------------------------------------------------------------------------

    /**
     * Должен вернуть пустой массив, если у процесса нет полей.
     */
    public function test_getFieldsAsArray_returns_empty_array_when_no_fields(): void
    {
        $process = $this->makeProcessMock([]);

        $result = ProcessService::getFieldsAsArray($process);

        $this->assertSame([], $result);
    }

    /**
     * Должен собрать массивы из каждого поля через asArray().
     */
    public function test_getFieldsAsArray_maps_fields_to_arrays(): void
    {
        $field1 = $this->makeFieldMock(['type' => 'text',   'name' => 'Имя',     'value' => 'Алекс']);
        $field2 = $this->makeFieldMock(['type' => 'number', 'name' => 'Возраст', 'value' => 25]);

        $process = $this->makeProcessMock([$field1, $field2]);

        $result = ProcessService::getFieldsAsArray($process);

        $this->assertCount(2, $result);
        $this->assertSame(['type' => 'text',   'name' => 'Имя',     'value' => 'Алекс'], $result[0]);
        $this->assertSame(['type' => 'number', 'name' => 'Возраст', 'value' => 25],      $result[1]);
    }

    /**
     * Порядок полей должен сохраняться.
     */
    public function test_getFieldsAsArray_preserves_field_order(): void
    {
        $fields = array_map(
            fn(int $i) => $this->makeFieldMock(['index' => $i]),
            range(0, 4)
        );

        $process = $this->makeProcessMock($fields);
        $result  = ProcessService::getFieldsAsArray($process);

        foreach (range(0, 4) as $i) {
            $this->assertSame($i, $result[$i]['index']);
        }
    }

    // -------------------------------------------------------------------------
    // fromData (статический)
    // -------------------------------------------------------------------------

    /**
     * fromData должен возвращать ProcessInterface.
     */
    public function test_fromData_returns_process_interface(): void
    {
        $result = ProcessService::fromData('Процесс', [
            ['type' => 'text', 'name' => 'Поле', 'value' => 'данные'],
        ]);

        $this->assertInstanceOf(ProcessInterface::class, $result);
    }

    /**
     * fromData должен корректно восстанавливать имя процесса.
     */
    public function test_fromData_sets_correct_name(): void
    {
        $process = ProcessService::fromData('ВосстановленныйПроцесс', []);

        $this->assertSame('ВосстановленныйПроцесс', $process->getName());
    }

    /**
     * fromData с пустым payload не должен бросать исключение.
     */
    public function test_fromData_accepts_empty_payload(): void
    {
        $this->expectNotToPerformAssertions();
        ProcessService::fromData('ПустойПроцесс', []);
    }

    /**
     * Данные, сериализованные через getFieldsAsArray, должны
     * корректно восстанавливаться через fromData (round-trip).
     */
    public function test_roundtrip_getFieldsAsArray_and_fromData(): void
    {
        $pdo = $this->makeInMemoryPdo();

        $name   = 'Оригинал';
        $fields = [
            ['type' => 'text',   'name' => 'title', 'value' => 'Hello'],
            ['type' => 'number', 'name' => 'count', 'value' => 42],
        ];
        $dto = new ProcessDTO($name, $fields);

        $service  = new ProcessService($dto, $pdo);
        $original = $service->ProcessCreator($dto->fields, $dto->name);

        $raw       = ProcessService::getFieldsAsArray($original);
        $restored  = ProcessService::fromData($dto->name, $raw);
        $restoredRaw = ProcessService::getFieldsAsArray($restored);

        $this->assertSame($raw, $restoredRaw, 'Round-trip должен сохранять все данные полей');
    }
}