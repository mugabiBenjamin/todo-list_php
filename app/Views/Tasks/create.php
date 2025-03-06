<?php session_start(); use App\Helpers\Security; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Create Task</title>
</head>

<body>
    <form action="/tasks/store" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
        <input type="text" name="name" placeholder="Task Name" required>
        <button type="submit">Create</button>
    </form>
</body>

</html>