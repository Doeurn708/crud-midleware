<?php
    $host = "localhost";
    $dbname = "db_crud_middleware";
    $username = "root";
    $password = "";   
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",  
            $username,
            $password,
            [          
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
       
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => 'DB connection failed: ' . $e->getMessage()  
            ]));
    }
?>