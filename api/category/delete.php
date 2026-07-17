<?php 
    require_once __DIR__ .'/../../config/database.php';
    require_once __DIR__ .'/../../middleware/auth.php';

    requireAuthAjax();

    header('Content-Type : application/json');

    $id = (int)($_POST['id'] ?? 0);

    if($id <= 0){
        echo json_encode(['success' => false, 'message' => 'ID fail']);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true ,'message' => 'delete category success']);

?>