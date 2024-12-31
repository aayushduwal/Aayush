function addToCart() {
  console.log("addToCart function called");
  const productId = $("#product_id").val();
  const productName = $("h1").text();
  const price = $("h4").text().replace("$", "");
  const quantity = parseInt($("#quantity").val());

  console.log("Data being sent:", {
    productId,
    productName,
    price,
    quantity,
  });

  $.post(
    "../cart/cart_handler.php",
    {
      action: "add",
      productId: productId,
      productName: productName,
      price: parseFloat(price),
      quantity: quantity,
    },
    function (response) {
      console.log("Server response:", response);
      updateCartBadge();
      alert("Product added to cart!");
    }
  ).fail(function (xhr, status, error) {
    console.error("Ajax error:", error);
  });
}

function updateCartBadge() {
  $.post("../cart/cart_handler.php", { action: "count" }, function (response) {
    const totalItems = parseInt(response);
    const cartBadge = $("#cart-badge");

    if (totalItems > 0) {
      cartBadge.css("visibility", "visible").text(totalItems);
    } else {
      cartBadge.css("visibility", "hidden");
    }
  });
}

$(document).ready(function () {
  updateCartBadge();
  $(".btn:not(.buy-now)").click(function (e) {
    e.preventDefault();
    addToCart();
  });
});
