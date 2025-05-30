<?php

include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


$product_id = $_GET['id'] ?? '';
$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT image FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Product not found or unauthorized.";
    header("Location: dashboard.php");
    exit;
}

$product = $result->fetch_assoc();
$image = $product['image'];


$stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$stmt->close();


if ($image && file_exists("uploads/" . $image)) {
    unlink("uploads/" . $image);
}

$_SESSION['message'] = "Product deleted successfully.";
header("Location: dashboard.php");
exit;
?>