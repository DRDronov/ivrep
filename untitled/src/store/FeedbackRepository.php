<?php

namespace Classes\store;

use Classes\Database;
use PDOException;
use PDO;

class FeedbackRepository{
    private Database $data;

    public function __construct(Database $database){
        $this->data = $database;
    }

    public function findById(int $id): array{
        $stmt = $this->data->prepare("SELECT * FROM feedbacks WHERE id = :id");
        $stmt->execute(array("id" => $id));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$res){
            return array('error' => 'Feedback not found');
        }else{
            return $res;
        }
    }

    public function count(): int{
        $stmt = $this->data->prepare("SELECT COUNT(*) FROM feedbacks");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function find(int $page = 1, int $perPage = 20): array{
        $offset = ($page - 1) * $perPage;
        $totalPages = ceil($this->count()/$perPage);

        $stmt = $this->data->prepare("SELECT * FROM feedbacks ORDER BY id DESC LIMIT :offset, :perPage ");
        $stmt->execute([':offset' => $offset, ':perPage' => $perPage]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($res) == 0){
            return array('error' => 'Feedback not found');
        }else{
            return array("feedbacks" => $res, "totalPages" => $totalPages);
        }
    }

    public function delete($id) : array{
        try {
            $stmt = $this->data->prepare("DELETE FROM feedbacks WHERE id = :id");
            $stmt->execute(array("id" => $id));
            $rowCount = $stmt->rowCount();
            if ($rowCount === 0) {
                throw new \Exception("No feedback with ID $id found.");
            }
            return array("success" => "Delete is successful");
        } catch (\Exception $e) {
            return array('error' => $e->getMessage());
        }
    }

    public  function getFieldsForRead() : array{
        return [
            'id' => 'int',
            'author' => 'string',
            'content' => 'string'
        ];
    }
    public  function getFieldsForCreate() : array{
        return [
            'author' => 'string',
            'content' => 'string'
        ];
    }

    public function create(array $arr) : array{
        $fields = $this->getFieldsForCreate();

        if(empty($arr['content']) || empty($arr['author'])){
            return ['error' => 'Author or Content are missing'];
        }

        $fieldNames = implode(', ', array_keys($fields));
        $placeholders = ':' . implode(', :', array_keys($fields));

        $validData = array_intersect_key($arr, $fields);

        $sql = "INSERT INTO feedbacks ($fieldNames) VALUES ($placeholders)";

        $stmt = $this->data->prepare($sql);
        try {
            $stmt->execute($validData);
            return ['success' => 'Feedback created successfully'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

}