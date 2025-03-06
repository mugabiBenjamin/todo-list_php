<?php
use App\Helpers\Security;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Create New Task</h2>
        <form action="/tasks" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
            <label for="name">Task Name:</label><br>
            <input type="text" id="name" name="name" required maxlength="255" pattern="[A-Za-z0-9\s\-_.,!?]{3,255}"><br>
            <small>Task name must be 3-255 characters.</small><br><br>
            <button type="submit">Create Task</button>
        </form>
        <a href="/">Back to Task List</a>
    </div>
</body>

</html>