<?php
session_start(); 

$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "bakery_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_token'])) {
    header("Location: classic_flavor.php"); 
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string(trim($_POST['username'])); 
    $password = $_POST['password']; 

    $stmt = $conn->prepare("SELECT admin_id, password FROM admin_tbl WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $hashedPassword);
        $stmt->fetch();


        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_token'] = bin2hex(random_bytes(32));
            $_SESSION['admin_id'] = $admin_id; 
            $_SESSION['role'] = 'admin'; 
            $_SESSION['welcome_message'] = "Welcome Admin!"; 
            header("Location: product.php"); 
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $stmt->close();
        $stmt = $conn->prepare("SELECT customer_id, password, role FROM customer_tbl WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($customer_id, $hashedPassword, $role);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_token'] = bin2hex(random_bytes(32));
                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['role'] = $role; 
                header("Location: product.php"); 
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
    $stmt->close();
}

$conn->close();
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="sign_in.css">
</head>
<body>
    <div class="container">
        <p id="tits">Your Pastry Favorites Are Waiting! Log In to Order!</p>
        <div id="signin-form">
            <p id="title">LOGIN FIRST</p><br>
            <form action="sign_in.php" method="post">
                <input type="text" name="username" id="username" placeholder="Username" required autofocus /><br><br>
                <input type="password" name="password" id="password" placeholder="Password" required /><br><br>
                <button type="submit" class="btnsign-in">Sign In</button>
            </form>
            <?php if (!empty($error)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <br>
            <p class="sp">Don't have an account? <a href="sign-up.php">Sign Up</a></p>
        </div>
    </div>
</body>
</html>