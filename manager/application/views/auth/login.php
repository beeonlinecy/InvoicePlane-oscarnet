<html>
<head>
<link rel="stylesheet" href="/manager/public/assets/bootstrap/css/bootstrap.min.css">
<script src="/manager/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<div class="container my-5">
    <h2 class="text-center mb-4">Login</h2>
    
    <form method="post" action="login/auth" class="mx-auto w-25 bg-light p-3 rounded">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" name="email" required class="form-control" id="email">
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" required class="form-control" id="password">
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>

