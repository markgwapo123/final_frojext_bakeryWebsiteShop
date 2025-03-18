<?php
session_start();

if (!isset($_SESSION['user_token']) || $_SESSION['role'] !== 'admin') {
    header("Location: sign_in.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $sql = "DELETE FROM product_tbl WHERE product_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "Product deleted successfully.";
    } else {
        echo "Error deleting product: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>