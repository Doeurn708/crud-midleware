<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Work, Explore & Repeat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { height: 100vh; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .main-card { width: 900px; height: 500px; overflow: hidden; border-radius: 15px; display: flex; }
        
        /* Left Side */
        .brand-side { 
            background-color: #4e54c8; 
            color: white; 
            padding: 40px;
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
        }
        .brand-side img { width: 80%; border-radius: 10px; margin-bottom: 20px; }
        
        /* Right Side */
        .form-side { background: white; padding: 40px; display: flex; align-items: center; justify-content: center; }
        .form-container { width: 100%; }
        
        .btn-primary { background-color: #4e54c8; border: none; padding: 0.75rem; font-weight: 600; }
        .btn-primary:hover { background-color: #3d42a6; }
    </style>
</head>
<body>

<div class="main-card shadow">
    <div class="col-md-6 brand-side d-none d-md-flex">
        <img src="https://i.pinimg.com/736x/76/14/c4/7614c4a4e2219c3b72e698df09df531c.jpg" alt="Work">
        <h3 class="fw-bold text-center">Answer at register</h3>
    </div>

    <div class="col-md-6 form-side">
        <div class="form-container">
            <h2 class="mb-1">Login</h2>
            <p class="text-muted mb-4">Enter your username and password to log in</p>
            
            <div id="alertBox"></div>
            
            <form id="form">
                <div class="mb-3">
                    <label for="email" class="form-label">Username</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
                </div>
                <div class="mb-3 text-end">
                    <a href="register.php" class="text-decoration-none small">Register?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">LOGIN</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function(){
        $('#form').submit(function(e){
            e.preventDefault();
            $.ajax({
                url: "../api/auth-handler.php",
                method: 'POST',
                dataType: 'json',
                data: { action: 'login', email: $('#email').val(), password: $('#password').val() },
                success: function(res){
                    if(res.success){
                        $('#alertBox').html(`<div class="alert alert-success">${res.message}</div>`);
                        window.location.href = res.role === 'admin' ? '../admin/dashboard.php' : '../client/index.php';
                    } else {
                        $('#alertBox').html(`<div class="alert alert-danger">${res.message || 'Login failed'}</div>`);
                    }
                }
            });
        });
    });
</script>
</body>
</html>
