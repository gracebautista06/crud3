<?php
// -------------------------------------------------------
// edit.php — Edit an existing task in the To-Do List
// Uses MySQLi with prepared statements to prevent SQL injection
// -------------------------------------------------------

include 'db.php'; // Include the database connection

// -------------------------------------------------------
// STEP 1: Get the task ID from the URL (?id=X)
// If no ID is provided or it's not a number, redirect home.
// -------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET['id']; // Cast to integer for extra safety

// -------------------------------------------------------
// STEP 2: Fetch the existing task from the database
// We use a prepared statement to safely pass the ID.
// -------------------------------------------------------
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);   // "i" = integer
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
$stmt->close();

// If no task was found with that ID, redirect home
if (!$task) {
    header("Location: index.php");
    exit();
}

// -------------------------------------------------------
// STEP 3: Handle the form submission (POST)
// This runs when the user clicks the "Update" button.
// -------------------------------------------------------
$error = "";   // Will hold any validation error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Input Validation ---
    $task_name = trim($_POST['task_name'] ?? '');

    if ($task_name === '') {
        $error = "Task name cannot be empty.";
    } elseif (strlen($task_name) > 255) {
        $error = "Task name is too long (max 255 characters).";
    } else {
        // --- Update the database using a prepared statement ---
        $update = $conn->prepare("UPDATE tasks SET task_name = ? WHERE id = ?");
        $update->bind_param("si", $task_name, $id); // "s" = string, "i" = integer
        $update->execute();
        $update->close();

        // Redirect back to index after a successful update
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Task — To-Do List</title>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ---- Base ---- */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: 50px auto;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* ---- Form ---- */
        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 4px;
            display: block;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            outline: none;
            box-sizing: border-box;
            font-size: 15px;
            transition: 0.3s;
        }

        input[type="text"]:focus {
            border-color: #6c63ff;
        }

        /* ---- Buttons ---- */
        .btn-row {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        button {
            padding: 10px 20px;
            border: none;
            background: #6c63ff;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        button:hover {
            background: #574fd6;
            transform: scale(1.05);
        }

        .btn-cancel {
            background: #aaa;
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #888;
            text-decoration: none;
            transform: scale(1.05);
        }

        /* ---- Error message ---- */
        .error {
            background: #ffe0e0;
            color: #cc0000;
            border: 1px solid #ffaaaa;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
        }

        /* ---- Meta info ---- */
        .meta {
            font-size: 13px;
            color: #999;
            margin-top: -8px;
        }

        /* ---- Icon helpers ---- */
        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 15px;
            pointer-events: none;
        }

        .input-wrapper input[type="text"] {
            padding-left: 36px;
        }

        label i {
            margin-right: 6px;
            color: #6c63ff;
        }

        .meta i {
            margin-right: 5px;
            color: #bbb;
        }

        .error i {
            margin-right: 6px;
        }

        button i, .btn-cancel i {
            margin-right: 6px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2><i class="fa-solid fa-pen-to-square"></i> Edit Task</h2>

    <!-- Show error message if validation failed -->
    <?php if ($error): ?>
        <div class="error"><i class="fa-solid fa-triangle-exclamation"></i><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!--
        The form posts back to the same page (edit.php?id=X).
        The current task_name is pre-filled in the input.
    -->
    <form class="edit-form" method="POST" action="edit.php?id=<?php echo $id; ?>">

        <div>
            <label for="task_name">
                <i class="fa-solid fa-list-check"></i>Task Name
            </label>
            <!--
                htmlspecialchars() prevents XSS by escaping special characters
                before echoing user data into the HTML.
            -->
            <div class="input-wrapper">
                <i class="fa-solid fa-pencil"></i>
                <input
                    type="text"
                    id="task_name"
                    name="task_name"
                    value="<?php echo htmlspecialchars($task['task_name']); ?>"
                    placeholder="Enter task name..."
                    required
                    maxlength="255"
                >
            </div>
        </div>

        <p class="meta">
            <i class="fa-regular fa-clock"></i>
            Created: <?php echo date('F j, Y, g:i a', strtotime($task['created_at'])); ?>
        </p>

        <div class="btn-row">
            <!-- Cancel goes back to index without saving -->
            <a class="btn-cancel" href="index.php">
                <i class="fa-solid fa-xmark"></i>Cancel
            </a>
            <button type="submit">
                <i class="fa-solid fa-floppy-disk"></i>Update Task
            </button>
        </div>

    </form>

</div>

</body>
</html>