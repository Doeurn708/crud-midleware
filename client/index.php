<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETEC Middleware</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">

        <a class="navbar-brand fw-bold" href="#">
            ETEC MIDDLEWARE
        </a>

        <div class="ms-auto d-flex align-items-center">

            <?php if(isset($_SESSION['user_name'])){ ?>

                <span class="text-white me-3">
                    Hello,
                    <strong><?= $_SESSION['user_name']; ?></strong>
                    (<?= $_SESSION['user_email']; ?>)
                </span>

                <button id="logout" class="btn btn-danger">
                    Logout
                </button>

            <?php }else{ ?>

                <a href="../frontend/login.php" class="btn btn-light">
                    Login
                </a>

            <?php } ?>

        </div>

    </div>
</nav>

<div class="container mt-5">
    <h2>Welcome to ETEC Middleware</h2>
    <p>This is the home page.</p>
</div>

<script>
$(document).ready(function(){

    $("#logout").click(function(){

        $.ajax({
            url: "../api/auth-handler.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "logout"
            },

            success: function(res){

                if(res.success){
                    window.location.href = '../frontend/login.php';
                }

            },

            
        });

    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
