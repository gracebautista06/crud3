<?php
require_once "db.php"; // your database connection file

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {

    $id = $_GET['id'];

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute delete
    if ($stmt->execute()) {
        header("Location: index.php?message=deleted");
        exit();
    } else {
        echo "Failed to delete task.";
    }

} else {
    echo "Invalid request.";
}
?>