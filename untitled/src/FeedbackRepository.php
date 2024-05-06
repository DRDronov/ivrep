<?php

namespace Classes;

use PDO;
class FeedbackRepository{
    private $data;

    public function __construct($database){
        $this->data = $database;
    }

    public function findById($id){

        $stmt = $this->data->prepare("SELECT * FROM feedbacks WHERE id = :id");
        $stmt->execute(array("id" => $id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function find($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->data->prepare("SELECT * FROM feedbacks LIMIT :offset, :perPage");
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":perPage", $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count() {
        $stmt = $this->data->prepare("SELECT COUNT(*) FROM feedbacks WHERE 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function delete($id)
    {
        $stmt = $this->data->prepare("DELETE FROM feedbacks WHERE id = :id");
        $stmt->execute(array("id" => $id));
        return true;
    }

    public  function getFieldsForRead()
    {
        return [
            'id' => 'int',
            'author' => 'string',
            'content' => 'string'
        ];
    }
    public  function getFieldsForCreate()
    {
        return [
            'author' => 'string',
            'content' => 'string'
        ];
    }

    public function create(array $arr)
    {
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