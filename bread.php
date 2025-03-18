 <?php
$servername = "localhost";
$username = "your_username"; 
$password = "your_password"; 
$dbname = "cartDB"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$data = json_decode(file_get_contents("php://input"), true);
$items = $data['items'];


$stmt = $conn->prepare("INSERT INTO cart_items (item_name, item_cost, item_image) VALUES (?, ?, ?)");
$stmt->bind_param("sds", $item_name, $item_cost, $item_image);


foreach ($items as $item) {
    $item_name = $item['itemName'];
    $item_cost = $item['itemCost'];
    $item_image = $item['itemImage'];
    $stmt->execute();
}


$stmt->close();
$conn->close();

echo json_encode(["message" => "Checkout data saved successfully"]);
?>