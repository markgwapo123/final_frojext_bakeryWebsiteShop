<?php
session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bakery_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if (isset($_POST['productId'])) {
        $productId = $_POST['productId'];

        $stmt = $conn->prepare("DELETE FROM product_tbl WHERE id = ?");
        $stmt->bind_param("i", $productId);

        if ($stmt->execute()) {
            echo "Product deleted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "No product ID provided.";
    }

    $conn->close();
} else {
    echo "You do not have permission to delete products.";
}
?>