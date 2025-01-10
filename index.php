<?php
session_start();

include_once './connection.php';
include_once './tasks/task_validation.php';
include_once './tasks/fetch_task.php';

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
    <div class="messages">
        <!-- Display error messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error" role="alert"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <!-- Display success messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success" role="alert"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
    </div>

    <div class="container-md">
        <h2>My To-Do List</h2>

        <form action="./tasks/add_task.php" method="POST">

            <input type="text" name="name" maxlength="50" placeholder="Add your task here..." value="<?php echo htmlspecialchars($task['name']); ?>">
            <input type="submit" value="Add" name="submit">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        </form>

        <?php if (!empty($tasks)): ?>
            <ul>
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <a href="./tasks/toggle_completion.php?id=<?php echo $task['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" class="task-toggle">
                            <input type="checkbox"
                                class="task-checkbox"
                                <?php echo $task['completed'] ? 'checked' : ''; ?>
                                tabindex="-1"
                                readonly>
                            <div class="task">
                                <div class="task-name <?php echo $task['completed'] ? 'completed' : ''; ?>">
                                    <?php echo htmlspecialchars($task['name']); ?>
                                </div>
                                <div class="delete-icon">
                                    <a href="./tasks/delete_task.php?id=<?php echo $task['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="warning">No tasks found. Add a new task to get started!</p>
        <?php endif; ?>
    </div>
</body>

</html>