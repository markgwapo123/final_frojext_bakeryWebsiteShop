<?php
session_start();

if (!isset($_SESSION['user_token'])) {
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

$sql = "SELECT product_id, prdct_name, prdct_price, prdct_img, quantity FROM product_tbl WHERE category = 'classic_flavor'";
$result = $conn->query($sql);

$products = [];

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Matteo boiiss</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="" name="keywords" />
  <meta content="" name="description" />

  <!-- Favicon -->
  <link href="img/favicon.ico" rel="icon" />

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Playfair+Display:wght@600;700&display=swap"
    rel="stylesheet" />

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Arizonia&display=swap" rel="stylesheet" />
  <!-- Libraries Stylesheet -->
  <link href="lib/animate/animate.min.css" rel="stylesheet" />
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />

  <!-- Template Stylesheet -->
  <link href="css/style.css" rel="stylesheet" />
  <link rel="stylesheet" href="classic_flavor.css" />
  <style>
    .classicFlavor {
      display: flex;
      flex-wrap: wrap;
    }

    .product {
      margin: 10px;
      text-align: center;
    }

    #addProductForm {
      margin-bottom: 20px;
      display:
        <?php echo isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'block' : 'none'; ?>
      ;
    }


    .modal {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
      margin: 15% auto;
      max-width: 500px;
    }

    .modal-content {
      background-color: #fdf5eb;
      border-radius: 5px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modal-header,
    .modal-footer {
      padding: 15px;
    }

    .modal-title {
      margin: 0;
    }

    #cartItems img,
    #modalCartItems img {
      width: 50px;
      height: 50px;
      margin-right: 10px;
      border-radius: 50%;

    }

    .tit {
      display: flex;
      justify-content: space-evenly
    }

    .quantity-display {
      font-weight: bold;
      margin-bottom: 5px;
      text-align: center;
    }

    .addquantity,
    .minusquantity {
      border: none;
      box-shadow: 0px 0px 7px 1px rgba(0, 0, 0, 0.5) inset;
      padding: 3px 10px 3px 10px;
      border-radius: 5px;
    }

    .quantity-container {
      display: flex;
      gap: 15px;
      align-items: baseline;
    }

    .addquantity:hover,
    .minusquantity:hover,
    .delete-butto:hover {
      padding: 5px 12px 5px 12px;
    }

    .delete-button {
      position: relative;
      left: 43%;
      border: none;
      background: transparent;
      color: red;
      cursor: pointer;
      font-weight: bolder;
    }

    .delete-button:hover {
      color: blue;
    }

    .product:hover {
      width: calc(32.33% - 82px);
      height: 260px;
    }

    .remove-button {
      border: none;
      background: transparent;
      color: red;
      cursor: pointer;
      font-weight: bold;
      margin-left: 2px;

    }

    .remove-button:hover {
      color: blue;
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

  </div>
  <div class="titles">
    <h1>Cake</h1>
  </div>


  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div class="dropdown">
      <button class="dropbtn" onclick="toggleDropdown(event)">Add Product</button>
      <div class="dropdown-content" id="dropdownContent" style="display: none;">
        <form id="addProduct" method="POST" action="add_product.php" enctype="multipart/form-data">
          <input type="text" name="productName" placeholder="Product Name" required />
          <input type="text" name="productPrice" placeholder="Product Price" required />
          <input type="number" name="productQuantity" placeholder="Product Quantity" required />
          <input type="file" name="productImage" accept="image/*" required />
          <input type="hidden" name="category" value="classic_flavor" />
          <button type="submit">Add Product</button>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <div id="cart" style="display: none;">
    <h2>Your Cart</h2>
    <ul id="cartItems"></ul>
    <hr>
    <div id="totalPrice" style="font-weight: bold;">Total: ₱0.00</div>
    <button id="checkoutButton" onclick="checkout()">Checkout</button>
    <button class="del_btn" onclick="closeCart()">Close</button> <!-- Close button -->
  </div>

  <h1 class="cakes">Classic Flavors</h1>
  <div class="classicFlavor">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $product): ?>
        <div class="product">
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <button class="delete-button" onclick="deleteProduct(<?php echo $product['product_id']; ?>)">X</button>
          <?php endif; ?>

          <!-- Display the product image -->
          <?php if (!empty($product['prdct_img'])): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['prdct_img']); ?>"
              alt="<?php echo htmlspecialchars($product['prdct_name']); ?>" />
          <?php else: ?>
            <img src="img/default-image.png" alt="Default Image" />
          <?php endif; ?>

          <h5><?php echo htmlspecialchars($product['prdct_name']); ?> -
            ₱<?php echo htmlspecialchars($product['prdct_price']); ?></h5>

          <div class="quantity-container">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <button class="minusquantity"
                onclick="changeQuantity('<?php echo htmlspecialchars($product['prdct_name']); ?>', -1)">-</button>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] != 'admin'): ?>
              <span><strong>Quantity: </strong></span>
            <?php endif; ?>


            <span class="quantity-display"
              id="quantity-<?php echo htmlspecialchars($product['prdct_name']); ?>"><?php echo htmlspecialchars($product['quantity']); ?></span>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <button class="addquantity"
                onclick="changeQuantity('<?php echo htmlspecialchars($product['prdct_name']); ?>', 1)">+</button>
            <?php endif; ?>
          </div>

          <button style="border:none; background:none;" class="plusbtn"
            data-name="<?php echo htmlspecialchars($product['prdct_name']); ?>"
            data-price="<?php echo htmlspecialchars($product['prdct_price']); ?>"
            quantity="<?php echo htmlspecialchars($product['quantity']); ?>"
            data-id="<?php echo isset($product['product_id']) ? htmlspecialchars($product['product_id']) : '0'; ?>"
            data-image="data:image/jpeg;base64,<?php echo base64_encode($product['prdct_img']); ?>"
            onclick="addToCart(this)">
            <img src="img/plus.png" alt="Add" style="width: 25px; height: 25px; vertical-align: middle;">
          </button>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No products found.</p>
    <?php endif; ?>
  </div>
  <div class="flavorsbtn">
    <a href="cake_Gourmet & Unique Flavors.php" class="guf">Gourmet & Unique Flavors</a><br />
    <br />
    <a href="cake_Seasonal_&_Festive_Flavors.php" class="sff">Seasonal & Festive Flavors</a>
  </div>
  <!-- Product End -->
  <div class="container-fluid bg-dark text-light footer my-6 mb-0 py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-lg-3 col-md-6">
          <h4 class="text-light mb-4">Quick Links</h4>
          <a class="btn btn-link" href="index.php">Home</a>
          <a class="btn btn-link" href="about.php">About Us</a>
          <a class="btn btn-link" href="product.php">Products</a>
          <a class="btn btn-link" href="contact.php">Contact Us</a>
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
  <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

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
  <!-- <script src="cart.js"></script> -->
  <script src="cart.js"></script>



  <div id="checkoutModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Checkout</h5>
        <button type="button" class="close" onclick="closeModal()" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <!-- Order Code -->
        <div id="generatedCode" style="font-weight: bold; margin-bottom: 15px;"></div>

        <!-- Product List Titles -->
        <div class="tit">
          <h6 style="margin-left:50px">Product</h6>
          <h6>Price</h6>
          <h6>Quantity</h6>
          <h6>Total</h6>
        </div>

        <!-- Cart Items -->
        <ul id="modalCartItems"></ul>

        <!-- Total Price -->
        <div id="modalTotalPrice" style="font-weight: bold; text-align: end;">Total: ₱0.00</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btns btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="button" class="btns btn-primary" onclick="confirmCheckout()">Confirm Checkout</button>
      </div>
    </div>
  </div>
</div>

</body>

</html>