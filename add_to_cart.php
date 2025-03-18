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

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productId = $_POST['productId'];

$sql = "SELECT product_id, prdct_name, prdct_price, prdct_img FROM product_tbl WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $productName = $product['prdct_name'];
    $productPrice = $product['prdct_price'];
    $productImage = base64_encode($product['prdct_img']);

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$productId] = [
            'name' => $productName,
            'price' => $productPrice,
            'image' => $productImage,
            'quantity' => 1
        ];
    }
}

$conn->close();
echo json_encode(['status' => 'success']);
?>