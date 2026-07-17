<?php 
    require_once __DIR__ .'/../../config/database.php';

    header("Content-Type: application/json");

    $stmt =$pdo->query("SELECT * FROM categories ORDER BY id DESC");
    $data = $stmt->fetchAll();

    echo json_encode(['success' => true ,'data' =>$data]);
?>