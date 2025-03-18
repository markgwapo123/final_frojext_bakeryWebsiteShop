<?php
session_start();

if (!isset($_SESSION['user_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
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

// Get the product name and new quantity from the POST request
$productName = $_POST['productName'];
$newQuantity = $_POST['newQuantity'];

// Update the quantity in the database
$sql = "UPDATE product_tbl SET quantity = ? WHERE prdct_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $newQuantity, $productName);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
}

$stmt->close();
$conn->close();
?>