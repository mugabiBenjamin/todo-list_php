<?php

use App\Helpers\CsrfGuard;

$csrf = new CsrfGuard();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - To-Do List</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="auth-page">
    <div class="container-md">
        <h2>Register</h2>
        <form action="/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="you@example.com">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="Min. 8 characters">
            <small>Password must be at least 8 characters.</small><br><br>

            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Confirm your password"><br><br>

            <button type="submit" class="btn-primary">Register</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="/login">Login here</a>.</p>
    </div>
</body>

</html>