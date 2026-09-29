<?php

use App\Helpers\CsrfGuard;

$csrf = new CsrfGuard();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="create-page">
    <div class="container-md">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Create New Task</h2>
            <form action="/logout" method="POST" style="margin: 0;">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">
                <button type="submit" class="btn-danger">Logout</button>
            </form>
        </header>

        <form action="/tasks" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">
            <input type="text" id="name" name="name" required maxlength="255" pattern="[A-Za-z0-9\s\-_.,!?]{3,255}"
                placeholder="Type your task here"><br>
            <div><button type="submit">Create Task</button></div>
            <small>Task name must be 3-255 characters.</small><br><br>
        </form>
        <a href="/">Back to Task List</a>
    </div>
</body>

</html>