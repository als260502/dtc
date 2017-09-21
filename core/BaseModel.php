<?php
namespace Core;

use PDO;


abstract class BaseModel
{
    private $pdo;

    protected $table;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(){

        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }
    public function findById(int $id){

        $query = "SELECT * FROM {$this->table} WHERE id=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT );
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public function create(array $data)
    {
        $dataArray = $this->prepareDataToInsert($data);

        $query = "INSERT INTO {$this->table} ({$dataArray[0]}) VALUES ({$dataArray[1]})";
        $stmt = $this->pdo->prepare($query);

        for ($i = 0; $i < count($dataArray[2]); $i++)
        {
            $stmt->bindValue("{$dataArray[2][$i]}", $dataArray[3][$i]);
        }

        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }

    public function update(array $data, int $id)
    {
        $dataArray = $this->prepareDataToUpdate($data);

        $query = "UPDATE {$this->table} SET {$dataArray[0]} WHERE id=:id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        for ($i = 0; $i < count($dataArray[1]); $i++)
        {
            $stmt->bindValue("{$dataArray[1][$i]}", $dataArray[2][$i]);
        }

        $result = $stmt->execute();
        $stmt->closeCursor();

        return $result;
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id=:id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $result = $stmt->execute();
        $stmt->closeCursor();

        return $result;


    }

    private function prepareDataToInsert(array $data)
    {
        $strKeys = "";
        $strBinds= "";
        $binds = [];
        $values = [];

        foreach ($data as $key => $value)
        {
            $strKeys .= ", {$key}";
            $strBinds .= ", :{$key}";

            $binds[] = ":{$key}";
            $values[] = $value;
        }

        $strKeys = substr($strKeys, 1);
        $strBinds = substr($strBinds, 1);

        return [$strKeys, $strBinds, $binds, $values];

    }

    private function prepareDataToUpdate(array $data)
    {
        $strKeysBinds = "";
        $binds = [];
        $values = [];

        foreach ($data as $key => $value)
        {
            $strKeysBinds .= ", {$key}=:{$key}";

            $binds[] = ":{$key}";
            $values[] = $value;
        }

        $strKeysBinds = substr($strKeysBinds, 1);

        return [$strKeysBinds, $binds, $values];

    }

}