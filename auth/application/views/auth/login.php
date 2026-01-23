<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<?php if ($this->session->flashdata('error')): ?>
    <p style="color:red;">
        <?= $this->session->flashdata('error') ?>
    </p>
<?php endif; ?>

<form method="post" action="/auth/login/auth">
    <p>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </p>

    <p>
        <label>Password</label><br>
        <input type="password" name="password" required>
    </p>

    <button type="submit">Login</button>
</form>

</body>
</html>
