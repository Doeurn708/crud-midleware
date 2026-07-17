<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Work, Explore & Repeat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { height: 100vh; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .main-card { width: 900px; height: 550px; overflow: hidden; border-radius: 15px; display: flex; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        .brand-side { background-color: #4e54c8; color: white; padding: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .brand-side img { width: 80%; border-radius: 10px; margin-bottom: 20px; }
        
        .form-side { background: white; padding: 40px; display: flex; align-items: center; justify-content: center; }
        .form-container { width: 100%; }
        
        .btn-primary { background-color: #4e54c8; border: none; padding: 0.75rem; font-weight: 600; }
        .btn-primary:hover { background-color: #3d42a6; }
    </style>
</head>
<body>

<div class="main-card">
    <div class="col-md-6 brand-side d-none d-md-flex">
        <img src="https://i.pinimg.com/736x/76/14/c4/7614c4a4e2219c3b72e698df09df531c.jpg" alt="Brand Art">
        <h3 class="fw-bold text-center">Create Now Love Now</h3>
    </div>

    <div class="col-md-6 form-side">
        <div class="form-container">
            <h2 class="mb-1">Create Account</h2>
            <p class="text-muted mb-4">Sign up to get started</p>
            
            <div id="alertBox"></div>
            
            <form id="form">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">REGISTER</button>
                <div class="text-center">
                    Already have an account? <a href="login.php" class="text-decoration-none">Login</a>
                </div>
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
                method: "POST",
                dataType: "json",
                data: {
                    action: 'register',
                    name: $('#name').val(),
                    email: $('#email').val(),
                    password: $('#password').val()
                },
                success: function(res){
                    if(res.success){
                        $('#alertBox').html(`<div class="alert alert-success">${res.message}</div>`);
                        //Optional: Redirect after success
                        setTimeout(() => { window.location.href = 'login.php'; }, 500);
                    } else {
                        $('#alertBox').html(`<div class="alert alert-danger">${res.message}</div>`);
                    }
                }
            });
        });
    });
</script>
</body>
</html>