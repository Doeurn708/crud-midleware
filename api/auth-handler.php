<?php 
    include_once '../config/database.php';

    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    header("Content-Type: application/json");
    $action = $_POST['action'] ?? '';

   

    // login
    if($action == 'login'){
        $email = trim(($_POST['email'] ?? ''));
        $password = trim(($_POST['password'] ?? ''));
        
        if($email === '' || $password === ''){
            echo json_encode(["success" => false , "message" => "All fields required"]);
            exit();
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt ->execute([$email]);
        $user = $stmt->fetch();

        if($user && password_verify($password ,$user['password'])){
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];

            echo json_encode([
                "success" => true, 
                "message" => "User logged in successfully",
                "role" => $user['role']
                ]);
        }else{
            echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        }
        exit();
    }


    // register
    if($action == 'register'){
        $name =trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if($name === '' || $email === ''|| $password ===''){
            echo json_encode(["success" => false,"message" => "All field required "]);
            exit ;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "message" => "Please enter a valid email address."]);
            exit;
        }

        $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $check->execute([$email]);
        if($check->fetch()){
            echo json_encode(['success' => false, "message" => "Email already exist"]);
            exit();
        }
        $hash = password_hash($password,PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users(name,email,password) VALUES (?,?,?)");
        $stmt->execute([$name,$email,$hash]);
        echo json_encode(["success" => true,"message" => "User registered successfully"]);
        exit();
        
    }
    
    // logout
    if($action == 'logout'){
        $_SESSION= [];
        session_destroy();
        echo json_encode(["success" => true , "message" => "User Logged out success"]);
        exit();
    }

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Unknown action."]);
?>
