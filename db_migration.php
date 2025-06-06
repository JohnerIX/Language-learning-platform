<?php
// db_migration.php
require_once __DIR__ . '/includes/config.php';

function addColumnIfNotExists($conn, $table, $column, $definition) {
    $stmt = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($stmt->rowCount() === 0) {
        $conn->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        echo "✅ Added column '$column' to table '$table'.\n";
    } else {
        echo "✔️ Column '$column' already exists in '$table'.\n";
    }
}

function tableExists($conn, $table) {
    $stmt = $conn->query("SHOW TABLES LIKE '$table'");
    return $stmt->rowCount() > 0;
}

try {
    // Check if 'users' table exists (required for foreign key in 'courses')
    if (!tableExists($conn, 'users')) {
        throw new Exception("Referenced table 'users' does not exist. Please create it before running this migration.");
    }

    // Create 'courses' table
    $conn->exec("CREATE TABLE IF NOT EXISTS `courses` (
        `course_id` INT AUTO_INCREMENT PRIMARY KEY,
        `tutor_id` INT NOT NULL,
        `title` VARCHAR(100) NOT NULL,
        `description` TEXT NOT NULL,
        `language` VARCHAR(50) NOT NULL,
        `level` VARCHAR(50) NOT NULL,
        `category` VARCHAR(255) DEFAULT NULL,
        `thumbnail_url` VARCHAR(255) DEFAULT NULL,
        `price` DECIMAL(10,2) DEFAULT 0,
        `is_free` TINYINT(1) DEFAULT 0,
        `status` ENUM('draft','pending','published','rejected') DEFAULT 'draft',
        `duration_minutes` INT DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`tutor_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "✔️ 'courses' table created or already exists.\n";

    // Create 'course_sections' table
    $conn->exec("CREATE TABLE IF NOT EXISTS `course_sections` (
        `section_id` INT AUTO_INCREMENT PRIMARY KEY,
        `course_id` INT NOT NULL,
        `title` VARCHAR(100) NOT NULL,
        `sort_order` INT NOT NULL DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "✔️ 'course_sections' table created or already exists.\n";

    // Create 'lessons' table
    $conn->exec("CREATE TABLE IF NOT EXISTS `lessons` (
        `lesson_id` INT AUTO_INCREMENT PRIMARY KEY,
        `course_id` INT NOT NULL,
        `section_id` INT NOT NULL,
        `title` VARCHAR(100) NOT NULL,
        `sort_order` INT NOT NULL DEFAULT 0,
        `lesson_type` ENUM('text','video_url','pdf_file','audio_file') NOT NULL,
        `content` TEXT DEFAULT NULL,
        `video_url` VARCHAR(255) DEFAULT NULL,
        `file_path` VARCHAR(255) DEFAULT NULL,
        `duration_minutes` INT DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE,
        FOREIGN KEY (`section_id`) REFERENCES `course_sections`(`section_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "✔️ 'lessons' table created or already exists.\n";

    // Create 'course_materials' table
    $conn->exec("CREATE TABLE IF NOT EXISTS `course_materials` (
        `material_id` INT AUTO_INCREMENT PRIMARY KEY,
        `course_id` INT NOT NULL,
        `title` VARCHAR(100) NOT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "✔️ 'course_materials' table created or already exists.\n";

    // Ensure category column exists (useful if this script is re-run and it was previously missing)
    addColumnIfNotExists($conn, 'courses', 'category', 'VARCHAR(255) DEFAULT NULL AFTER `level`');

    echo "\n🎉 Database schema verified and updated successfully!\n";

} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";

    if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        echo "\n⚠️ NOTE: Foreign key constraint error detected.\n";
        echo "Make sure the referenced tables (especially 'users') exist first.\n";
    }
} catch (Exception $ex) {
    echo "❌ Error: " . $ex->getMessage() . "\n";
}
