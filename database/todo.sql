-- Drop the database if it already exists
DROP DATABASE IF EXISTS `todo_list`;

-- Create the database
CREATE DATABASE IF NOT EXISTS `todo_list`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

-- Use the created database
USE `todo_list`;

-- Drop the tasks table if it already exists
DROP TABLE IF EXISTS `tasks`;

-- Create the tasks table with additional columns and enhancements
CREATE TABLE `tasks` (
    `id` INT NOT NULL AUTO_INCREMENT,                      -- Primary key for the task
    `name` VARCHAR(50) NOT NULL,                          -- Name of the task
    `created_at` TIMESTAMP(6) NULL DEFAULT CURRENT_TIMESTAMP(6), -- Timestamp for when the task was created
    PRIMARY KEY (`id`)                                     -- Define the primary key
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data into the tasks table
INSERT INTO `tasks` 
VALUES
(1,'starter','2024-11-18 09:42:39');

-- Dump completed on 2024-11-18 12:46:52
