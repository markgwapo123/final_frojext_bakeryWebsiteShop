<?php
session_start();
if (!isset($_SESSION['user_token']) || $_SESSION['role'] !== 'admin') {
    header("Location: sign_in.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Add Account</title>
    <link rel="stylesheet" href="add_account.css">
</head>

<body>

    <form action="process_add_account.php" method="POST">
        <h1>Add New Account</h1><br>
        <input type="text" name="username" placeholder="Username" id="username"required /><br><br>
        <input type="password" name="password" placeholder="Password" id="password" required /><br><br>
        <select name="role" required>
            <option value="admin">Admin</option><br><br>
        </select>
        <button type="submit">Add Account</button>
    </form>
</body>

</html>