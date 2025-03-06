<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <title>Tasks</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
    <h1>Tasks</h1>
    <a href="/tasks/create">Create Task</a>
    <ul>
        <?php foreach ($tasks as $task): ?>
        <li>
            <input type="checkbox" <?php echo $task['completed'] ? 'checked' : ''; ?>
                onclick="location.href='/tasks/toggle?id=<?php echo $task['id']; ?>'">
            <span><?php echo htmlspecialchars($task['name']); ?></span>
            <a href="/tasks/edit?id=<?php echo $task['id']; ?>">Edit</a>
            <a href="/tasks/delete?id=<?php echo $task['id']; ?>">Delete</a>
        </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>