<?php include 'db.php'; ?>
<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<?php
if (!isset($_SESSION['user_id'])) header("Location: index.php");

$id = $_GET['id'];
$uid = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM products WHERE id=$id AND user_id=$uid");
$product = $result->fetch_assoc();

if (!$product) die("Access denied.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['product_name']);
    $price = trim($_POST['price']);
    $sku = trim($_POST['sku']);
    $image = $product['image']; 
    if ($name == "" || $price == "" || $sku == "") {
        echo "<div class='alert alert-danger'>All fields are required.</div>";
    } else {
        if ($_FILES['image']['name']) {
            if (!empty($product['image']) && file_exists("uploads/" . $product['image'])) {
                unlink("uploads/" . $product['image']);
            }
            $image = $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
        }
        $stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, sku=?, image=? WHERE id=? AND user_id=?");
        $stmt->bind_param("sdssii", $name, $price, $sku, $image, $id, $uid);
        $stmt->execute();
        header("Location: dashboard.php");
        exit();
    }
}
?>

<div class="container mt-5 col-md-6">
    <h2>Edit Product</h2>
    <form id="editProductForm" method="POST" enctype="multipart/form-data" novalidate>
       <div class="mb-3">
       <label>Product Name</label>
        <input type="text" name="product_name" id="product_name" class="form-control border-primary border-1 my-2" value="<?= htmlspecialchars($product['product_name']) ?>" required>
          <small class="text-danger error-product-name"></small>
       </div>
        <div class="mb-3">
        <label>Price</label>
        <input type="number" name="price" id="price" class="form-control border-primary border-1 my-2" value="<?= htmlspecialchars($product['price']) ?>" required>
        <small class="text-danger error-price"></small>
        </div>
        <div class="mb-3">
        <label>SKU</label>
        <input type="text" name="sku" id="sku" class="form-control border-primary border-1 my-2" value="<?= htmlspecialchars($product['sku']) ?>" required>
        <small class="text-danger error-sku"></small>
        </div>
        <div class="mb-3">
        <label>Product Image</label>
        <small class="form-text text-primary fst-italic ms-2">Leave blank to retain current image</small>
        <input type="file" name="image" id="image" class="form-control border-primary border-1 my-2">
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" width="100" class="my-2 d-block">
        <small class="text-danger error-image"></small>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>


<script>
$(document).ready(function () {
   $('#editProductForm').on('submit',function(e){
    e.preventDefault();
    let isValid=true;
    $('.text-danger').text('');
    const product_name=$('#product_name').val().trim();
    const price=$('#price').val().trim();
    const sku=$('#sku').val().trim();
    if(product_name===''){
        $('.error-product-name').text("Product name is required");
        isValid=false;
    }
    if(price===''){
        $('.error-price').text("Valid price is required");
        isValid=false;
    }
    if(sku===''){
        $('.error-sku').text("sku is required");
        isValid=false;
    }
    if(isValid){
        this.submit();
    }
   });

   
});
</script>

<?php include 'footer.php'; ?>
