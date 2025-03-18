<?php
session_start();

if (!isset($_SESSION['user_token']) || $_SESSION['role'] !== 'admin') {
    header("Location: sign_in.php");
    exit();
}

$db_servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "bakery_db";

$conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT product_id, prdct_name, prdct_price, prdct_img, category, quantity FROM product_tbl";
$result = $conn->query($sql);

$categories = ['cake' => [], 'bread' => [], 'cookies' => []];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[$row['category']][] = $row;
    }
} else {
    echo "<p>No products found</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Matteo boiiss</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        .btn-update {
            background-color: green;
            color: black;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-update:hover {
            background-color: darkgreen;
            color: white;
        }

        .btn-delete {
            background-color: red;
            color: black;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: darkred;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-lg-0 px-lg-5">
            <div class="collapse navbar-collapse">
                <div class="navbar-nav mx-auto p-4 p-lg-0">
                    <a href="index.php" class="nav-item nav-link" id="home">Home</a>
                    <?php if (!isset($_SESSION['user_token'])): ?>
                        <a href="sign_in.php" class="nav-item nav-link" id="product">Products</a>
                    <?php else: ?>
                        <a href="product.php" class="nav-item nav-link active" id="product">Products</a>
                    <?php endif; ?>
                    <a href="about.php" class="nav-item nav-link" id="about">About</a>
                    <a href="contact.php" class="nav-item nav-link" id="contact">Contact</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin/add_account.php" class="nav-item nav-link" id="addAccount">Add Account</a>
                        <a href="product_list.php" class="nav-item nav-link" id="prdct_list">Product List</a>
                        <a href="inventory.php" class="nav-item nav-link" id="inventory">Inventory</a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-item nav-link" id="logout">Log Out</a>
                </div>
            </div>
        </nav>
    </nav>

    <div class="titles">
        <h1 style="text-align:center;">Product List</h1>
    </div>


    <div class="container mt-5">
        <?php
        foreach ($categories as $category => $products) {
            if (!empty($products)) {
                echo "<h3>" . ucfirst($category) . " Products</h3>";  // Display category name as heading
                echo "<table class='table table-bordered' style='border: 2px solid black;'>";
                echo "<thead><tr><th>Product ID</th><th>Name</th><th>Price</th><th>Quantity</th><th>Image</th><th>Category</th><th>Actions</th></tr></thead><tbody>";

                foreach ($products as $product) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($product['product_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($product['prdct_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($product['prdct_price']) . "</td>";
                    echo "<td>" . htmlspecialchars($product['quantity']) . "</td>";

                    if (!empty($product['prdct_img'])) {
                        $imageData = base64_encode($product['prdct_img']);
                        echo "<td><img src='data:image/jpeg;base64," . $imageData . "' alt='" . htmlspecialchars($product['prdct_name']) . "' style='width: 50px; height: auto;'></td>";
                    } else {
                        echo "<td>No Image</td>";
                    }

                    echo "<td>" . htmlspecialchars($product['category']) . "</td>";

                    // Add Update and Delete buttons with hover effects
                    echo "<td>";
                    echo "<a href='update.php?product_id=" . htmlspecialchars($product['product_id']) . "' class='btn-update'>Update</a> ";
                    echo "<a href='delete.php?product_id=" . htmlspecialchars($product['product_id']) . "' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>";
                    echo "</td>";

                    echo "</tr>";
                }

                echo "</tbody></table>";
            }
        }
        ?>
    </div>

    <?php
    $conn->close();
    ?>



    <div class="container-fluid bg-dark text-light footer my-6 mb-0 py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="index.php">Home</a>
                    <a class="btn btn-link" href="about.php">About Us</a>
                    <a class="btn btn-link" href="product.html">Products</a>
                    <a class="btn btn-link" href="contact.html">Contact Us</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Address</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>31-39 Bonifacio St, Kabankalan City, Negros
                        Occidental</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>09654254444</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>Matteo.boiis@gmail.com</p>
                </div>
                <!-- Add Google Map Embed below the address -->
                <div class="col-lg-6 col-md-12">
                    <h4 class="text-light mb-4">Our Location</h4>
                    <!-- Google Maps Embed iframe with API key -->
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3131.1536586403063!2d122.81334557385453!3d9.99281499011223!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33ac11d993b05013%3A0x63fac4ab483af001!2sCafe%20Matteo!5e1!3m2!1sen!2sph!4v1732636038621!5m2!1sen!2sph"
                        width="400" height="150" style="border:0; border-radius:10px" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright text-light py-4 wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a href="#">Matteo.boiis</a>, All Right Reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i
            class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>