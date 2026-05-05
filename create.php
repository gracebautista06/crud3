<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 80px auto;
            background: #fff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="text"] {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
            transition: 0.3s;
        }

        input[type="text"]:focus {
            border-color: #6c63ff;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        button {
            padding: 11px 25px;
            border: none;
            background: #6c63ff;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            transition: 0.3s;
        }

        button:hover {
            background: #574fd6;
            transform: scale(1.05);
        }

        .btn-cancel {
            background: #aaa;
        }

        .btn-cancel:hover {
            background: #888;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6c63ff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>➕ Create New Task</h2>

    <?php
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
        $task = trim($_POST['task_name']);

        if (!empty($task)) {
            $stmt = $conn->prepare("INSERT INTO tasks (task_name) VALUES (?)");
            $stmt->bind_param("s", $task);

            if ($stmt->execute()) {
                $message = '<div class="alert-success">✅ Task added successfully! <a href="index.php">View all tasks</a></div>';
            } else {
                $message = '<div class="alert-error">❌ Failed to add task. Please try again.</div>';
            }

            $stmt->close();
        } else {
            $message = '<div class="alert-error">⚠️ Task name cannot be empty.</div>';
        }
    }

    echo $message;
    ?>

    <form method="POST">
        <div class="form-group">
            <label for="task_name">Task Name</label>
            <input
                type="text"
                id="task_name"
                name="task_name"
                placeholder="Enter your task here..."
                required
                autofocus
            >
        </div>

        <div class="btn-group">
            <button type="submit" name="add">Add Task</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php'">Cancel</button>
        </div>
    </form>

    <a class="back-link" href="index.php">← Back to To-Do List</a>

</div>

</body>
</html>