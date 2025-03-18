<?php
session_start(); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$cartData = json_decode(file_get_contents('php://input'), true);

if (empty($cartData)) {
    echo json_encode(['success' => false, 'message' => 'No cart data received.']);
    exit;
}

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in.']);
    exit;
}

$customerId = $_SESSION['customer_id'];
$customerQuery = "SELECT cust_fname, cust_lname FROM customer_tbl WHERE customer_id = ?";
$stmt = $conn->prepare($customerQuery);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$customerResult = $stmt->get_result();

if ($customerResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
    exit;
}

$customer = $customerResult->fetch_assoc();

$insertQuery = "INSERT INTO payment_tbl (customer_id, cust_fname, cust_lname, product_id, prdct_price, quantity, prdct_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);

$response = [];
$response['success'] = true;

foreach ($cartData as $item) {
    $productName = $item['name'];
    $quantity = isset($item['quantity']) ? $item['quantity'] : 1; 

    $productQuery = "SELECT product_id, prdct_price, quantity FROM product_tbl WHERE prdct_name = ?";
    $productStmt = $conn->prepare($productQuery);
    $productStmt->bind_param("s", $productName);
    $productStmt->execute();
    $productResult = $productStmt->get_result();

    if ($productResult->num_rows === 0) {
        $response['success'] = false;
        $response['message'] = "Product not found for name: $productName.";
        error_log($response['message']); 
        break;
    }

    $product = $productResult->fetch_assoc();
    $productId = $product['product_id'];
    $productPrice = $product['prdct_price'];

    if ($product['quantity'] < $quantity) {
        $response['success'] = false;
        $response['message'] = "Not enough quantity for product: $productName.";
        error_log($response['message']); 
        break;
    }

    $stmt->bind_param("issdiss", $customerId, $customer['cust_fname'], $customer['cust_lname'], $productId, $productPrice, $quantity, $productName);
    
    if (!$stmt->execute()) {
        $response['success'] = false;
        $response['message'] = "Error inserting payment for product ID $productId: " . $stmt->error;
        error_log($response['message']);
        break;
    }

    $updateQuantityQuery = "UPDATE product_tbl SET quantity = quantity - ? WHERE product_id = ?";
    $updateStmt = $conn->prepare($updateQuantityQuery);
    $updateStmt->bind_param("ii", $quantity, $productId);

    if (!$updateStmt->execute()) {
        $response['success'] = false;
        $response['message'] = "Error updating quantity for product ID $productId: " . $updateStmt->error;
        error_log($response['message']);
        break;
    }

    $productStmt->close();
 }

$stmt->close();
$conn->close();

echo json_encode($response);
?>