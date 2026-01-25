<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form method="post" action="login/auth">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>

    <?php if ($this->session->flashdata('error')): ?>
        <p><?= $this->session->flashdata('error') ?></p>
    <?php endif ?>
</form>

</body>
</html>
