<?php

use App\Helpers\Security;
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
        <form action="/update/<?php echo (int)$task['id']; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
            <label for="name">Task Name:</label><br>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($task['name']); ?>" required
                maxlength="255" pattern="[A-Za-z0-9\s\-_.,!?]{3,255}"><br>
            <small>Task name must be 3-255 characters.</small><br><br>
            <label for="completed">Completed:</label><br>
            <input type="checkbox" id="completed" name="completed"
                <?php echo $task['completed'] ? 'checked' : ''; ?>><br><br>
            <button type="submit">Update Task</button>
        </form>
        <a href="/">Back to Task List</a>
    </div>
</body>

</html>