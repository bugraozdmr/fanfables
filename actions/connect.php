<?php 
    try {
        $db = new PDO("mysql:host=localhost;dbname=FANFABLES;charset=utf8mb4",'grant','grant345');
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>