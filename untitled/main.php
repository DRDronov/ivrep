<?php
//использовал для заполнения таблицы


//
//require_once "vendor/autoload.php";
//require_once "config.php";
//
//$database = new \Classes\Database();
//
//try{
//    $stmt = $database->prepare("INSERT INTO reviews (author, content) VALUES (:author, :content)");
//
//    for($i = 2; $i < 100; $i++){
//        $author = "author" . ($i+1);
//        $content = "content" . ($i+1);
//
//        $stmt->bindParam(":author", $author);
//        $stmt->bindParam(":content", $content);
//        $stmt->execute();
//    }
//    echo "success";
//}catch(\PDOException $e){
//    echo $e->getMessage();
//}
