const cart = {};
let cartVisible = false;
function addToCart(button) {
  const productName = button.getAttribute("data-name");
  const productPrice = parseFloat(button.getAttribute("data-price"));
  const productImage = button.getAttribute("data-image");
  const productId = button.getAttribute("data-id");
  let availableQuantity = parseInt(button.getAttribute("quantity"));

  console.log(productId);
  console.log(productImage);
  console.log(productPrice);
  console.log(productPrice);
  console.log(productName);
  console.log(availableQuantity);


  if (availableQuantity <= 0) {
    alert("The product is unavailable.");
    return;
  }


  if (cart[productName]) {
    if (cart[productName].quantity < availableQuantity) {
      cart[productName].quantity += 1; 
    } else {
      alert("No more stock available for this product.");
      return;
    }
  } else {
    cart[productName] = {
      id: productId,
      price: productPrice,
      quantity: 1, 
      image: productImage,
    };
  }

  availableQuantity -= 1;
  button.setAttribute("data-quantity", availableQuantity);
  updateProductQuantity(productId, availableQuantity); 

  updateCartDisplay();
  showCart();
  updateTotalPrice();
}
function showCart() {
  const cartDiv = document.getElementById("cart");

  if (Object.keys(cart).length === 0) {
    cartDiv.style.display = "none";
    alert("Your cart is empty.");
  } else {
    cartDiv.style.display = "block";
  }

  cartVisible = true;
}
function updateProductQuantity(productId, newQuantity) {
  $.ajax({
    url: "update_quantity.php",
    method: "POST",
    data: {
      product_id: productId,
      newQuantity: newQuantity,
    },
    success: function (response) {
      console.log(response);
    },
    error: function (xhr, status, error) {
      console.error(error);
    },
  });
}

function closeCart() {
  const cartDiv = document.getElementById("cart");
  cartDiv.style.display = "none";
  cartVisible = false;
}

function changeQuantity(productName, change) {
  const quantityElement = document.getElementById(`quantity-${productName}`);
  let currentQuantity = parseInt(quantityElement.textContent);

  currentQuantity += change;

  if (currentQuantity < 0) {
    currentQuantity = 0;
  }

  quantityElement.textContent = currentQuantity;

  $.ajax({
    url: "update_quantity.php",
    method: "POST",
    data: {
      productName: productName,
      newQuantity: currentQuantity,
    },
    success: function (response) {
      console.log(response);
    },
    error: function (xhr, status, error) {
      console.error(error);
    },
  });
}
function updateCartDisplay() {
  const cartItems = document.getElementById("cartItems");
  cartItems.innerHTML = "";

  if (Object.keys(cart).length === 0) {
    closeCart();
    return;
  }

  for (const product in cart) {
    const item = cart[product];
    const newItem = document.createElement("div");
    newItem.className = "cart-item";
    newItem.innerHTML = `
      <img src="${item.image}" alt="${product}" />
      <span>${product} - ₱${item.price.toFixed(2)} (Quantity: ${
      item.quantity
    })</span>
      <button style="color:red; font-weight: bolder; border:none; background:transparent" onclick="removeProduct('${product}')">X</button>
    `;
    cartItems.appendChild(newItem);
  }
}

function updateTotalPrice() {
  let total = 0;
  for (const product in cart) {
    total += cart[product].price * cart[product].quantity;
  }
  document.getElementById("totalPrice").textContent = `Total: ₱${total.toFixed(
    2
  )}`;
}

function checkout() {
  updateModalCartDisplay();
  const checkoutModal = document.getElementById("checkoutModal");
  checkoutModal.style.display = "block";
}

function updateModalCartDisplay() {
  const modalCartItems = document.getElementById("modalCartItems");
  modalCartItems.innerHTML = "";

  for (const product in cart) {
    const item = cart[product];
    const productTotal = item.price * item.quantity;
    const newItem = document.createElement("div");
    newItem.className = "modal-cart-item";
    newItem.innerHTML = `
            <img src="${item.image}" alt="${product}" />
            <span style="margin-left: 5px;">${product}</span>
            <span style="margin-left: 5px;"> ₱${item.price.toFixed(2)}</span>
            <span style="margin-left: 10px;">(Qty: ${item.quantity})</span>
            <span style="margin-left: 60px;"> ₱${productTotal.toFixed(2)}</span>
        `;
    modalCartItems.appendChild(newItem);
  }

  updateModalTotalPrice();
}

function updateModalTotalPrice() {
  let total = 0;
  for (const product in cart) {
    total += cart[product].price * cart[product].quantity;
  }
  document.getElementById(
    "modalTotalPrice"
  ).textContent = `Total: ₱${total.toFixed(2)}`;
}

function confirmCheckout() {
  const cartData = [];
  for (const product in cart) {
    cartData.push({
      product_id: cart[product].id,
      name: product,
      price: cart[product].price,
      quantity: cart[product].quantity,
      image: cart[product].image,
    });
  }

  fetch("checkout.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(cartData),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Checkout successful! Thank you for your purchase.");
        for (const product in cart) {
          delete cart[product];
        }
        updateCartDisplay();
        updateTotalPrice();
        window.location.href = "payment.php";
      } else {
        alert("Checkout failed: " + data.message);
      }
      closeModal();
    })
    .catch((error) => {
      console.error("Error during checkout:", error);
      alert("An error occurred during checkout. Please try again. Check the console for details.");
      closeModal();
    });
}

function closeModal() {
  const checkoutModal = document.getElementById("checkoutModal");
  checkoutModal.style.display = "none";
}

function toggleDropdown(event) {
  event.stopPropagation();
  var dropdownContent = document.getElementById("dropdownContent");
  dropdownContent.style.display =
    dropdownContent.style.display === "block" ? "none" : "block";
}

window.onclick = function (event) {
  if (
    !event.target.matches(".dropbtn") &&
    !event.target.closest("#dropdownContent")
  ) {
    var dropdownContent = document.getElementById("dropdownContent");
    dropdownContent.style.display = "none";
  }
};

function deleteProduct(productId) {
  if (confirm("Are you sure you want to delete this product?")) {
    $.ajax({
      type: "POST",
      url: "delete_product.php",
      data: { product_id: productId },
      success: function (response) {
        alert(response);
        location.reload();
      },
      error: function () {
        alert("An error occurred while deleting the product.");
      },
    });
  }
}
function removeProduct(productName) {
  delete cart[productName];

  updateCartDisplay();
  updateTotalPrice();

  console.log(`${productName} removed from the cart (frontend only).`);
}
function closeCart() {

  document.getElementById("cart").style.display = "none";
 
}

//code for OR

function generateRandomCode(length = 8) {
  const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let result = "";
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * characters.length));
  }
  return result;
}

function checkout() {
  const generatedCode = generateRandomCode();
  document.getElementById("generatedCode").textContent = `Order Code: ${generatedCode}`;

  updateModalCartDisplay();

  const checkoutModal = document.getElementById("checkoutModal");
  checkoutModal.style.display = "block";
}
