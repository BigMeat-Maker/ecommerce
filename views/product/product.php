<?php
session_start();
require_once($_SERVER["DOCUMENT_ROOT"] . "/app/config/Directories.php");

// Include database connection
include(ROOT_DIR . "app/config/DatabaseConnect.php");
$db = new DatabaseConnect();
$conn = $db->connectDB();

// Initialize variables
$product = [];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category = ["1" => "Electronics", "2" => "Fashion", "3" => "Home Appliance"];

// Fetch product details
if ($id > 0) {
    try {
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $error_message = "Product not found.";
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
} else {
    $error_message = "Invalid product ID.";
}

// Include header
require_once(ROOT_DIR . "includes/header.php");

// Handle session messages
if (isset($_SESSION["success"])) {
    $messageSuccess = $_SESSION["success"];
    unset($_SESSION["success"]);
}

if (isset($_SESSION["error"])) {
    $messageError = $_SESSION["error"];
    unset($_SESSION["error"]);
}
?>

<!-- Navbar -->
<?php require_once(ROOT_DIR . "includes/navbar.php"); ?>

<!-- Product Details -->
<div class="container my-5 bg-bpod">
    <div class="container mt-5">

        <?php if (isset($messageSuccess)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><?php echo htmlspecialchars($messageSuccess); ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if (isset($messageError)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><?php echo htmlspecialchars($messageError); ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger" role="alert">
                <strong><?php echo htmlspecialchars($error_message); ?></strong>
            </div>
        <?php } elseif ($product) { ?>

            <div class="row">
                <!-- Product Image -->
                <div class="col-md-6">
                    <img src="<?php echo htmlspecialchars(BASE_URL . ($product["image_url"] ?? "assets/images/default.png")); ?>"
                        alt="Product Image"
                        class="img-fluid border border-warning border-5"
                        style="height:500px">
                </div>

                <!-- Product Information -->
                <div class="col-md-6">
                    <form action="<?php echo htmlspecialchars(BASE_URL . "app/cart/add_to_cart.php"); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo intval($product["id"]); ?>">
                        <h2><?php echo htmlspecialchars($product["product_name"] ?? "Unknown Product"); ?></h2>
                        <div class="mb-3">
                            <span class="badge text-bg-info">
                                <?php echo htmlspecialchars($category[$product["category_id"]] ?? "Unknown Category"); ?>
                            </span>
                        </div>
                        <p class="lead text-warning fw-bold">
                            Php <?php echo number_format($product["unit_price"] ?? 0.00, 2); ?>
                        </p>
                        <p><?php echo htmlspecialchars($product["product_description"] ?? "No description available."); ?></p>

                        <!-- Quantity Selection -->
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" id="decrement-btn">-</button>
                                <input type="number" id="quantity" name="quantity" class="form-control text-center" value="1" min="1" max="<?php echo intval($product["stocks"] ?? 10); ?>" style="max-width: 60px;">
                                <button class="btn btn-outline-secondary" type="button" id="increment-btn">+</button>
                                <span class="input-group-text">/ Remaining Stocks: <?php echo intval($product["stocks"] ?? 0); ?></span>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"
                                <?php echo (($product["stocks"] ?? 0) <= 0 ? "disabled" : ""); ?>>
                                <?php echo (($product["stocks"] ?? 0) <= 0 ? "Sold Out" : "Add to Cart"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    // Quantity increment and decrement
    document.getElementById('decrement-btn').addEventListener('click', function() {
        let quantityInput = document.getElementById('quantity');
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    document.getElementById('increment-btn').addEventListener('click', function() {
        let quantityInput = document.getElementById('quantity');
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < parseInt(quantityInput.max)) {
            quantityInput.value = currentValue + 1;
        }
    });
</script>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2024 MyShop. All rights reserved.</p>
    <nav>
        <a href="#" class="text-white">Privacy Policy</a> |
        <a href="#" class="text-white">Terms & Conditions</a>
    </nav>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>