<?php
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID not found.");
}


$get = $conn->query("SELECT image FROM users WHERE id = $id");
if ($get->num_rows !== 1) {
    die("User not found.");
}
$row = $get->fetch_assoc();
$image = $row['image'];


$sql = "DELETE FROM users WHERE id = $id";
if ($conn->query($sql)) {
    if ($image && file_exists("uploads/$image")) {
        unlink("uploads/$image");
    }
    header("Location: myprofile.php");
    exit;
} else {
    echo "Error deleting user.";
}
?>