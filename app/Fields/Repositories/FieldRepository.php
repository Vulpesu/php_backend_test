<?php
namespace App\Fields\Repositories;

use App\Fields\Contracts\FieldRepositoryInterface;
use App\Fields\Contracts\FieldInterface;
use PDO;

// Вопрос связаности Field с Process, как они между собой как экземпляры класса и классы хранят о друг друге информацию или дают информацию
// Вопрос отношений между FieldRepository и ProcessRepository
class FieldRepository implements FieldRepositoryInterface
{
    // Привязан ли FieldRepository к ProcessRepository? и если да каким образом
    public function __construct(private PDO $pdo) {}

    function save(FieldInterface $Field){

        // Нужен метод достающий данные 
        if ($this->findByName($Field->getName()) === null) {
            $this->insert($Field);
        }

        return $this->update($Field);
    }
    
    function findById(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT id, name, process_id, json FROM fields WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch();

        // Надо подумать где разместить метод fromArray, как лучше хранить
        return $data ? Field::fromArray($data) : null;
    }

    // Вопрос того как определять process_id не перегружая метод аргументами и зависимостями
    function findByName(FieldInterface $Field)
    {
        $stmt = $this->pdo->prepare('SELECT id, name, process_id, json FROM fields 
                                    WHERE name = :name and process_id = :process_id');
                                    // Как находить ID процесса?
        $stmt->execute([':name' => $Field->getName(),
                        ':process_id' => /* Как то надо доставать связь Field-а с Process-ом */]);

        $data = $stmt->fetch();


        // Надо подумать где разместить метод fromArray, как лучше хранить
        return $data ? Field::fromArray($data) : null;
    }

    private function insert(FieldInterface $Field): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO field (name, process_id, json)
             VALUES (:name, :process_id, :json)'
        );

        // Метод формурующий JSON для БД
        $stmt->execute([
            ':name' => $Field->getName(),
            ':process_id'  => /* Как то надо доставать связь Field-а с Process-ом */ ,
            ':json'  => $Field->getJSON(),
        ]);

        // Возвращаем объект с присвоенным ID
        // return $this->findById((int) $this->pdo->lastInsertId());
        return $this->pdo->lastInsertId();
    }

    //private function 
}