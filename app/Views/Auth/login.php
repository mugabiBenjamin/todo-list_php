<?php

use App\Helpers\CsrfGuard;

$csrf = new CsrfGuard();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - To-Do List</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="auth-page">
    <div class="container-md">
        <h2>Login</h2>
        <form action="/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit" class="btn-primary">Login</button>
        </form>
        <p style="margin-top: 15px;">Don't have an account? <a href="/register">Register here</a>.</p>
    </div>
</body>

</html>