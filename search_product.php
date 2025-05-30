<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$search = '';
$user_id = $_SESSION['user_id'];
$products = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = trim($_POST['search']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ? AND (product_name LIKE ? OR sku LIKE ? OR CAST(price AS CHAR) LIKE ?)");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("isss", $user_id, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h3 class="mb-3">Search Products</h3>
    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control border-dark border-1" placeholder="Search by Name, SKU or Price" required value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if (!empty($products)) : ?>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td><?= htmlspecialchars($product['sku']) ?></td>
                        <td>₹<?= number_format($product['price'], 2) ?></td>
                        <td>
                            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" width="60">
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
        <p class="mt-4 text-danger">No products found for "<?= htmlspecialchars($search) ?>"</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>