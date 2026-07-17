<?php 
    // Insert category 
    require_once __DIR__ ."/../../config/database.php";
    require_once __DIR__ ."/../../middleware/auth.php";

    requireAuthAjax();

    header('Content-Type:application/json');

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');


    if($name === ''){
        echo json_encode(['success' => false , 'message' => "Excuse me you input category"]);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO categories (name,description) VALUE(?,?)");
    $stmt->execute([$name,$description]);

    echo json_encode(['success' => true , 'message' => 'Add category success']);

?>