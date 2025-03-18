
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Update Product</h3>
                    </div>
                    <div class="card-body">
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

                            // Fetch current product details
                            $stmt = $conn->prepare("SELECT * FROM product_tbl WHERE product_id = ?");
                            $stmt->bind_param("i", $product_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $product = $result->fetch_assoc();

                            if (!$product) {
                                echo "<div class='alert alert-danger'>Product not found.</div>";
                                exit;
                            }
                        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            // Handle form submission for update
                            $product_id = intval($_POST['product_id']);
                            $name = $_POST['prdct_name'];
                            $price = $_POST['prdct_price'];
                            $quantity = $_POST['quantity'];
                            $category = $_POST['category'];
                            $image = $_FILES['prdct_img']['tmp_name'] ? file_get_contents($_FILES['prdct_img']['tmp_name']) : null;

                            // Update query
                            $stmt = $conn->prepare(
                                "UPDATE product_tbl SET prdct_name = ?, prdct_price = ?, quantity = ?, prdct_img = COALESCE(?, prdct_img), category = ? WHERE product_id = ?"
                            );
                            $stmt->bind_param("sdissi", $name, $price, $quantity, $image, $category, $product_id);

                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success'>Product updated successfully.</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Error updating product: " . $conn->error . "</div>";
                            }

                            $stmt->close();
                            $conn->close();
                            exit;
                        }
                        ?>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                            <div class="mb-3">
                                <label for="prdct_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="prdct_name" name="prdct_name" value="<?php echo htmlspecialchars($product['prdct_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="prdct_price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="prdct_price" name="prdct_price" value="<?php echo htmlspecialchars($product['prdct_price']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="prdct_img" class="form-label">Image</label>
                                <input type="file" class="form-control" id="prdct_img" name="prdct_img">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Update Product</button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
