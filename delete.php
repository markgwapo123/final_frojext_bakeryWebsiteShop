<?php
session_start();

$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "bakery_db";

$conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Delete query
    $stmt = $conn->prepare("DELETE FROM product_tbl WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "Product deleted successfully.";
    } else {
        echo "Error deleting product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
