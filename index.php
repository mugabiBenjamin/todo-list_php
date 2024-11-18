<?php
session_start();

include_once './connection.php';
include_once './php/task_validation.php';
include_once './php/fetch_task.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/todo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <title>To-Do</title>
</head>

<body>
    <div class="container-md">
        <h2>My To-Do List</h2>

        <form action="./php/add_task.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
            <input type="text" name="name" placeholder="Add your text here..." value="<?php echo htmlspecialchars($task['name']); ?>">
            <input type="submit" value="Add" name="submit">
        </form>

        <!-- Display error messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <!-- Display success messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
            <div class="success">Task added successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
            <div class="success">Task deleted successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
            <div class="success">Task updated successfully!</div>
        <?php endif; ?>

        <?php if (!empty($tasks)): ?>
            <ul>
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <div class="task-name"><?php echo htmlspecialchars($task['name']); ?></div>
                        <div class="task-icons">
                            <a href="./php/update_task.php?id=<?php echo $task['id']; ?>"><i class="bi bi-pencil-fill"></i></a>
                            <a href="./php/delete_task.php?id=<?php echo $task['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>">
                                <i class="bi bi-trash3-fill"></i>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="warning">No tasks found. Add a new task to get started!</p>
        <?php endif; ?>
    </div>
</body>

</html>