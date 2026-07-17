<?php 
    require_once __DIR__ . "/../../config/database.php";
    require_once __DIR__ . "/../../middleware/auth.php";

    requireAuthAjax();


    header('Content-Type: application/json');

    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');


    if($id <= 0 || $name === ''){
        echo json_encode(['success' => false , 'message' => 'data fail']);
        exit;
    } 

    $stmt = $pdo->prepare("UPDATE categories SET name = ? ,description =? WHERE id = ? ");
    $stmt ->execute([$name ,$description,$id]);

    echo json_encode(['success' => true ,'message' => 'edit category success']);


?>
