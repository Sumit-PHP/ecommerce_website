<?php
include 'db.php';
include 'header.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM products WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<style>
    .table-bordered th,.table-bordered td{
        border:1px solid;
    }
</style>
<div class="container mt-4">
    <h2>Your Products</h2>
    <a href="add_product.php" class="btn btn-primary mb-3">Add New Product</a>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>price (INR)</th>
                <th>SKU</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['sku']) ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <a href="uploads/<?= htmlspecialchars($row['image']) ?>" target="_blank"><img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="50"></a>
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>