<?php

use App\Helpers\CsrfGuard;
use App\Models\Task;

/** @var Task $task */

$csrf = new CsrfGuard();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="edit-page">
    <div class="container-md">
        <h2>Edit Task</h2>

        <form action="/update/<?php echo (int) $task->id; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">

            <label for="name">Task name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($task->name); ?>" required
                maxlength="255" pattern="[A-Za-z0-9\s\-_.,!?]{3,255}">
            <small>Task name must be 3-255 characters.</small>

            <label for="completed">
                <input type="checkbox" id="completed" name="completed" value="1" <?php echo $task->completed ? 'checked' : ''; ?>>
                Completed
            </label>

            <button type="submit" class="btn-primary">Update Task</button>
        </form>
        <a href="/">Back to Task List</a>

        <form action="/logout" method="POST" class="logout-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</body>

</html>