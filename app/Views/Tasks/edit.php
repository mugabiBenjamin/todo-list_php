<?php session_start(); use App\Helpers\Security; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Task</title>
</head>

<body>
    <form action="/tasks/update?id=<?php echo $task['id']; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
        <input type="text" name="name" value="<?php echo htmlspecialchars($task['name']); ?>" required>
        <button type="submit">Update</button>
    </form>
</body>

</html>