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
        <h2>Create New Task</h2>

        <form action="/tasks" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">

            <label for="name">Task name</label>
            <input type="text" id="name" name="name" required maxlength="255"
                pattern="[A-Za-z0-9\s\-_.,!?]{3,255}" placeholder="Type your task here">
            <small>Task name must be 3-255 characters.</small>

            <button type="submit" class="btn-primary">Create Task</button>
        </form>
        <a href="/">Back to Task List</a>

        <form action="/logout" method="POST" class="logout-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</body>

</html>