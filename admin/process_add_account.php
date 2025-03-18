    <?php
    session_start();
    if (!isset($_SESSION['user_token']) || $_SESSION['role'] !== 'admin') {
        header("Location: sign_in.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $db_servername = "localhost";
        $db_username = "root"; 
        $db_password = ""; 
        $db_name = "bakery_db"; 

        $conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO admin_tbl (username, password, role) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $input_username = $_POST['username'];
        $input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt->bind_param("sss", $input_username, $input_password, $role);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "New Admin account created successfully!";
            header("Location: /RRM/product.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid request method.";
    }
    ?>