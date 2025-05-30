<?php
include 'db.php';
include 'header.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$name = $price = $sku = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['product_name']);
    $price = trim($_POST['price']);
    $sku = trim($_POST['sku']);
    $user_id = $_SESSION['user_id'];


    if (empty($name)) $errors['product_name'] = "Product name is required.";
    if (empty($price) || !is_numeric($price)) $errors['price'] = "Valid price is required.";
    if (empty($sku)) $errors['sku'] = "SKU is required.";
    if ($_FILES['image']['error'] == 4) $errors['image'] = "Product image is required.";

    if (empty($errors)) {
        $img_name = time() . "_" . basename($_FILES['image']['name']);
        $img_tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($img_tmp, "uploads/$img_name");

        $stmt = $conn->prepare("INSERT INTO products (user_id, product_name, price, sku, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("isdss", $user_id, $name, $price, $sku, $img_name);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Product added successfully!";
        header("Location: dashboard.php");
        exit;
    }
}
?>

<div class="container mt-4">
    <h2>Add New Product</h2>
    <form method="POST" enctype="multipart/form-data" id="productForm" novalidate>
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" id="product_name" class="form-control border-primary border-1" value="<?= htmlspecialchars($name) ?>">
            <small class="text-danger error-product-name"><?= $errors['product_name'] ?? '' ?></small>
        </div>

        <div class="mb-3">
            <label>Price (INR)</label>
            <input type="text" name="price" id="price" class="form-control border-primary border-1" value="<?= htmlspecialchars($price) ?>">
            <small class="text-danger error-price"><?= $errors['price'] ?? '' ?></small>
        </div>

        <div class="mb-3">
            <label>SKU</label>
            <input type="text" name="sku" id="sku" class="form-control border-primary border-1" value="<?= htmlspecialchars($sku) ?>">
            <small class="text-danger error-sku"><?= $errors['sku'] ?? '' ?></small>
        </div>

        <div class="mb-3">
            <label>Product Image</label>
            <input type="file" name="image" id="image" class="form-control border-primary border-1">
            <small class="text-danger error-image"><?= $errors['image'] ?? '' ?></small>
        </div>

        <button type="submit" class="btn btn-success">Add Product</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>


<script>
    $(document).ready(function() {
        $('#productForm').submit(function(e) {
            e.preventDefault();
            let isValid = true;
            $('.text-danger').text('');

            const product_name = $('#product_name').val().trim();
            const price = $('#price').val().trim();
            const sku = $('#sku').val().trim();
            const image = $('#image')[0].files[0];
            if (product_name === '') {
                $('.error-product-name').text("Product Name Is Required");
                isValid = false;
            }
            if (price === '') {
                $('.error-price').text("Valid Price Is Required");
                isValid = false;
            }
            if (sku === '') {
                $('.error-sku').text("SKU Is Required");
                isValid = false;
            }
            if (image === '') {
                $('.error-image').text("Product Image Is Required");
                isValid = false;
            }
            if (isValid) {
                this.submit();
            }
        });
    });
</script>

<?php include 'footer.php'; ?>