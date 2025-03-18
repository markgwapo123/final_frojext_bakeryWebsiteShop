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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['productName']);
    $productPrice = (float) $_POST['productPrice'];
    $productQuantity = (int) $_POST['productQuantity']; 
    $category = trim($_POST['category']);

    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['productImage']['type'];
        if (!in_array($fileType, $allowedTypes)) {
            die("Invalid file type. Only JPG, PNG, and GIF files are allowed.");
        }

        
        $productImage = file_get_contents($_FILES['productImage']['tmp_name']);
    } else {
        die("Image upload error.");
    }

    $stmt = $conn->prepare("INSERT INTO product_tbl (prdct_name, prdct_price, quantity, prdct_img, category) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    
    $stmt->bind_param("sdiss", $productName, $productPrice, $productQuantity, $productImage, $category);

    
    if ($stmt->execute()) {
        $_SESSION['welcome_message'] = "Product added successfully!";
    } else {
        die("Error adding product: " . $stmt->error);
    }

    $stmt->close();
}


$conn->close();


$redirectUrl = '';

switch ($category) {
    case 'classic_flavor':
        $redirectUrl = 'classic_flavor.php';
        break;
    case 'Seasonal_&_festive':
        $redirectUrl = 'cake_Seasonal_&_Festive_Flavors.php';
        break;
    case 'cake_gourmet':
        $redirectUrl = 'cake_Gourmet%20&%20Unique%20Flavors.php';
        break;
    case 'classic_bread':
        $redirectUrl = 'classic_bread.php';
        break;
    case 'gourmet_bread':
        $redirectUrl = 'bread_Gourmet%20&%20Unique%20Flavors.php';
        break;
    case 'seasonal_bread':
        $redirectUrl = 'bread_Seasonal_&_Festive_Flavors.php';
        break;
    case 'classic_cookies':
        $redirectUrl = 'classic_cookies.php';
        break;
    case 'seasonal_cookies':
        $redirectUrl = 'cookies_Seasonal_&_Festive_Flavors.php';
        break;
    case 'gourmet_cookies':
        $redirectUrl = 'cookies_Gourmet%20&%20Unique%20Flavors.php';
        break;
    default:
        $redirectUrl = 'default_page.php'; 
}


header("Location: " . $redirectUrl);
exit();
?>