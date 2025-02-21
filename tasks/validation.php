<?php

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateTaskName($name) {
    if (empty($name)) {
        return "Task name cannot be empty.";
    }
    if (strlen($name) > 50) {
        return "Task name cannot exceed 50 characters.";
    }
    if (!preg_match("/^[a-zA-Z0-9\s]+$/", $name)) {
        return "Only letters, numbers, and spaces are allowed.";
    }
    return ""; 
}
?>