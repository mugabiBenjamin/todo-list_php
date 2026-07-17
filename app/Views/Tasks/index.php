<?php

use App\Helpers\CsrfGuard;

$csrf = new CsrfGuard();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body class="index-page">
    <div class="container-md">
        <h2>My To-Do List</h2>
        <a href="/create">+ Add New Task</a>
        <?php if (empty($tasks)): ?>
            <p>No tasks yet. Add one above to get started.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <div class="task-item <?php echo $task->completed ? 'completed' : ''; ?>">
                            <?php echo htmlspecialchars($task->name); ?>
                        </div>
                        <div class="task-actions">
                            <form action="/update/<?php echo (int) $task->id; ?>" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">
                                <input type="hidden" name="name" value="<?php echo htmlspecialchars($task->name); ?>">
                                <input type="hidden" name="completed" value="<?php echo $task->completed ? '0' : '1'; ?>">
                                <button type="submit" class="btn-mark">
                                    <?php echo $task->completed ? 'Mark Incomplete' : 'Mark Complete'; ?>
                                </button>
                            </form>
                            <div class="edit-delete">
                                <a href="/edit/<?php echo (int) $task->id; ?>" class="edit-link">Edit</a>
                                <form action="/delete/<?php echo (int) $task->id; ?>" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf->generateToken(); ?>">
                                    <button type="submit" class="btn-danger" onclick="return confirm('Delete this task?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>

</html>