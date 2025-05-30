<?php
include 'db.php';

$name = $email = $phone = $password = $confirm_password = "";
$errors = [];
$response = ['success' => false, 'message' => '', 'errors' => []];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $image_name = $_FILES["image"]["name"];
    $image_tmp = $_FILES["image"]["tmp_name"];


    if (empty($name)) $errors['name'] = "Name is required.";
    if (empty($email)) $errors['email'] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";

    if (empty($phone)) $errors['phone'] = "Phone is required.";
    elseif (!preg_match("/^[0-9]{10}$/", $phone)) $errors['phone'] = "Phone must be 10 digits.";

    if (empty($password)) $errors['password'] = "Password is required.";
    if (empty($confirm_password)) $errors['confirm_password'] = "Confirm Password is required.";
    elseif ($password !== $confirm_password) $errors['confirm_password'] = "Passwords do not match.";

    if (empty($image_name)) $errors['image'] = "Image is required.";


    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $new_image_name = time() . "_" . basename($image_name);
        $upload_path = "./uploads/" . $new_image_name;

        if (move_uploaded_file($image_tmp, $upload_path)) {
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $new_image_name);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Registration successful!';
                $response['redirect'] = 'index.php';
            } else {
                $response['message'] = 'Database error: ' . $conn->error;
            }
        } else {
            $response['message'] = "Image upload failed.";
        }
    } else {
        $response['errors'] = $errors;
    }


    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container-fluid mt-5">
    <h2 class="text-primary">User Registration</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" id="registerForm" novalidate>
        <div class="row">
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" id="name" placeholder="Full Name" class="form-control border-primary border-3" value="<?= htmlspecialchars($name) ?>">
                <small class="text-danger error-name"></small>
            </div>

            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="email" placeholder="Email Address" class="form-control border-primary border-3" value="<?= htmlspecialchars($email) ?>">
                <small class="text-danger error-email"></small>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" placeholder="Phone" class="form-control border-primary border-3" value="<?= htmlspecialchars($phone) ?>">
                <small class="text-danger error-phone"></small>
            </div>

            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">Profile Image</label>
                <input type="file" name="image" id="image" class="form-control border-primary border-3">
                <small class="text-danger error-image"></small>
            </div>
        </div>



        <div class="row">
            <div class="col-md-6 mb-3 form-group position-relative">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" class="form-control border-primary border-3" autocomplete="new-password">
                <i class="bi bi-eye-slash toggle-password" toggle="#password" style="position: absolute; top: 38px; right: 10px; transform:translateY(-20%); cursor: pointer; font-size:24px;"></i>
                <small class="text-danger error-password"></small>
            </div>

            <div class="col-md-6 mb-3 form-group position-relative">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="form-control border-primary border-3" autocomplete="new-password">
                <i class="bi bi-eye-slash toggle-password" toggle="#confirm_password" style="position: absolute; top: 38px; right: 10px; transform:translateY(-20%); cursor: pointer; font-size:24px;"></i>
                <small class="text-danger error-confirm-password"></small>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </div>
        <div id="successMsg" class="alert alert-success d-none">Submit Form Successfully</div>


    </form>
</div>

<script>
    $(document).ready(function() {
        $(".toggle-password").click(function() {
            const input = $($(this).attr("toggle"));
            const type = input.attr("type") === "password" ? "text" : "password";
            input.attr("type", type);
            $(this).toggleClass("bi-eye bi-eye-slash");
        });


        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            $('.text-danger').text('');
            $('#successMsg').addClass('d-none').text('');


            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const phone = $('#phone').val().trim();
            const password = $('#password').val().trim();
            const confirm_password = $('#confirm_password').val().trim();
            const image = $('#image')[0].files[0];
            if (name === '') {
                $('.error-name').text("Name Is Required");
                isValid = false;
            }

            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (email === '') {
                $('.error-email').text("Email Is Required");
                isValid = false;
            } else if (!emailPattern.test(email)) {
                $('.error-email').text("Please Enter Valid Email");
                isValid = false;
            }

            if (phone === '') {
                $('.error-phone').text("Phone Is Required");
                isValid = false;
            }
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])[\S]{8,}$/;
            if (password === '') {
                $('.error-password').text("Password Is Required");
                isValid = false;
            } else if (!passwordPattern.test(password)) {
                $('.error-password').text("Password must be at least 8 characters long and include uppercase, lowercase, number, special character, and no spaces.");
                isValid = false;
            }
            if (confirm_password === '') {
                $('.error-confirm-password').text("Please Enter Confirm Password");
                isValid = false;
            } else if (password !== confirm_password) {
                $('.error-password').text("Password Do Not Match");
                isValid = false;
            }
            if (!image) {
                $('.error-image').text("Please Upload An Image");
                isValid = false;
            }
            if (isValid) {
                const formData = new FormData(this);
                $.ajax({
                    url: 'register.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#successMsg').removeClass('d-none').text(response.message);
                            $('#registerForm')[0].reset();
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 3000);
                        } else {

                            if (response.errors) {
                                for (const field in response.errors) {
                                    $('.error-${field}').text(response.errors[field]);
                                }
                            } else {
                                $('#successMsg').removeClass('d-none').addClass('alert-danger').text(response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        $('#successMsg').removeClass('d-none').addClass('alert-danger').text('An error occurred. Please try again.');
                    }
                });
            }
        });
    });
</script>
<?php include 'footer.php'; ?>