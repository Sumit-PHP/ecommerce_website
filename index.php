<?php

include 'db.php';

$email = $password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    
    if (empty($email)) $errors['email'] = "Email is required.";
    if (empty($password)) $errors['password'] = "Password is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION["user_id"] = $user_id;
                $_SESSION["user_name"] = $name;
                header("Location:dashboard.php");
                exit;
            } else {
                $errors['login'] = "Invalid email or password.";
            }
        } else {
            $errors['login'] = "Invalid email or password.";
        }
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-primary">User Login</h2>

    <?php if (!empty($errors['login'])): ?>
        <div class="alert alert-danger"><?= $errors['login'] ?></div>
    <?php endif; ?>

    <form action="" method="post" id="loginForm">
        <div class="row">
        <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control border-primary border-3" value="<?= htmlspecialchars($email) ?>" autocomplete="email">
            <small class="text-danger error-email"><?= $errors['email'] ?? '' ?></small>
        </div>

        <div class="form-group mb-3 position-relative">
            <label>Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control border-primary border-3" autocomplete="new-password">
                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                    <i class="fa-solid fa-eye" id="toggleIcon"></i>
                </button>
            </div>
            <small class="text-danger error-password"><?= $errors['password'] ?? '' ?></small>
        </div>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#togglePassword').on('click', function () {
        const passwordInput = $('#password');
        const icon = $('#toggleIcon');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        icon.toggleClass('fa-eye fa-eye-slash');
    });


        $('#loginForm').submit(function (e) {
            e.preventDefault();
        let isValid = true;
         $('.text-danger').text();
         const email=$('#email').val().trim();
         const password=$('#password').val().trim();
         if(email===''){
            $('.error-email').text("Enail is required");
            isValid=false;
         }
         if(password===''){
            $('.error-password').text("Password is required");
            isValid=false;
         }
         if(isValid){
            this.submit();
         }
    });

    });
</script>

<?php include 'footer.php'; ?>