<?php

namespace Classes;

use PDO;

require_once 'config.php';

class Database{

    private PDO $pdo;

    public function __construct() {
        $this->pdo = new PDO('sqlite:' . DB_FILE);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function prepare($statement) :\PDOStatement{
        return $this->pdo->prepare($statement);
    }



}