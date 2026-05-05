<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>To-Do List</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            margin: 0;
            padding: 0;
        }

        .container{
            width: 60%;
            margin: 50px auto;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        h2{
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* FORM */
        form{
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        input[type="text"]{
            flex: 1;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        input[type="text"]:focus{
            border-color: #6c63ff;
        }

        button{
            padding: 10px 15px;
            border: none;
            background: #6c63ff;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover{
            background: #574fd6;
            transform: scale(1.05);
        }

        /* TABLE */
        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th{
            background: #6c63ff;
            color: white;
            padding: 12px;
        }

        td{
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover{
            background: #f4f4ff;
        }

        /* UPDATE INPUT */
        .edit-input{
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        /* DELETE LINK */
        a{
            color: red;
            text-decoration: none;
            margin-left: 10px;
            font-weight: bold;
        }

        a:hover{
            text-decoration: underline;
        }

        .actions{
            display: flex;
            align-items: center;
            gap: 8px;
        }

    </style>
</head>

<body>

<div class="container">

<h2>📝 To-Do List</h2>

<!-- CREATE TASK -->
<form method="POST">
    <input type="text" name="task_name" placeholder="Enter task..." required>
    <button type="submit" name="add">Add</button>
</form>

<?php
// ADD TASK
if (isset($_POST['add'])) {
    $task = $_POST['task_name'];
    $conn->query("INSERT INTO tasks (task_name) VALUES ('$task')");
    header("Location: index.php");
}

// DELETE TASK
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id=$id");
    header("Location: index.php");
}

// UPDATE TASK
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $task = $_POST['task_name'];
    $conn->query("UPDATE tasks SET task_name='$task' WHERE id=$id");
    header("Location: index.php");
}
?>

<!-- READ TASKS -->
<table>
<tr>
    <th>Task</th>
    <th>Actions</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");

while ($row = $result->fetch_assoc()) {
?>
<tr>
    <td><?php echo $row['task_name']; ?></td>
    <td class="actions">

        <!-- EDIT FORM -->
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input class="edit-input" type="text" name="task_name" value="<?php echo $row['task_name']; ?>">
            <button type="submit" name="update">Update</button>
        </form>

        <!-- DELETE -->
        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this task?')">
            Delete
        </a>

    </td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>