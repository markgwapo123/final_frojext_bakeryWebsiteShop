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

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cust_fname = $conn->real_escape_string(trim($_POST['cust_fname']));
    $cust_lname = $conn-> real_escape_string(trim($_POST['cust_lname']));
    $signup_email = $conn->real_escape_string(trim($_POST['signup-email']));
    $phone_number = $conn->real_escape_string(trim($_POST['phone_number']));
    $cust_brgy = $conn->real_escape_string(trim($_POST['cust_brgy']));
    $cust_city = $conn->real_escape_string(trim($_POST['cust_city']));
    $cust_province = $conn->real_escape_string(trim($_POST['cust_province']));
    $signup_username = $conn->real_escape_string(trim($_POST['signup-username']));
    $signup_password = $_POST['signup-password'];
    $signup_confirm_password = $_POST['signup-confirm-password'];

    if ($signup_password !== $signup_confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM customer_tbl WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $signup_username, $signup_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($signup_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO customer_tbl (cust_fname, cust_lname, email, phone_number, cust_brgy, cust_city, cust_province, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $cust_fname, $cust_lname, $signup_email, $phone_number, $cust_brgy, $cust_city, $cust_province, $signup_username, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['signup_success'] = "Sign up successful! Please log in.";
                echo "<script>
                        alert('Sign up successful! Please log in.');
                        window.location.href = 'sign_in.php';
                      </script>";
                exit();
            } else {
                $error = "Error creating account. Please try again.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="sign-up.css" />
    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['signup_success'])): ?>
                alert("Sign up successful! You can now sign in.");
                <?php unset($_SESSION['signup_success']); ?>
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <div class="container">
        <p id="tits">Hungry for Fresh Pastries? Sign Up and Get the First Taste!</p>
        <div id="signup-form">
            <p id="title">CREATE AN ACCOUNT</p>
            <br />
            <form action="sign-up.php" method="post">
                <div class="form-row">
                    <input type="text" name="cust_fname" placeholder="First Name" required autofocus />
                    <input type="text" name="cust_lname" placeholder="Last Name" required />
                    <input type="email" name="signup-email" placeholder="Email" required />
                    <input type="text" name="phone_number" placeholder="Phone Number" required />
                    <input type="text" name="cust_brgy" placeholder="Barangay" required />
                    <input type="text" name="cust_city" placeholder="City" required />
                    <input type="text" name="cust_province" placeholder="Province" required />
                    <input type="text" name="signup-username" placeholder="Username" required />
                    <input type="password" name="signup-password" placeholder="Password" required />
                    <input type="password" name="signup-confirm-password" placeholder="Confirm Password" required />
                </div>
                <button type="submit" class="submitbtn">Sign Up</button>
            </form>
            <?php if (!empty($error)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <br />
            <p class="sp">
                Already have an account? <a href="sign_in.php" id="switchToSignIn">Sign In</a>
            </p>
        </div>
    </div>
</body>
</html>