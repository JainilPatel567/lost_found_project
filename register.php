<?php


session_start();

require_once "includes/config.php";

$error   = "";
$success = "";

if (isset($_POST["register"])) {

    
    $name       = trim($_POST["name"]);
    $email      = trim($_POST["email"]);
    $password   = trim($_POST["password"]);
    $confirm_pw = trim($_POST["confirm_pw"]);
    $contact_no = trim($_POST["contact_no"]);

    
    if (empty($name) || empty($email) || empty($password) || empty($contact_no)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_pw) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^[0-9]{10}$/', $contact_no)) {
        $error = "Contact number must be exactly 10 digits.";
    } else {

     
        $sql = "SELECT user_id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
        
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            $param_email = $email;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $error = "This email is already registered. Please log in.";
                } else {
                    
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    
                    $sql_insert = "INSERT INTO users (name, email, password, contact_no) VALUES (?, ?, ?, ?)";
                    if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                        // Bind parameters
                        mysqli_stmt_bind_param($stmt_insert, "ssss", $param_name, $param_email2, $param_pass, $param_contact);
                        
                        $param_name    = $name;
                        $param_email2  = $email;
                        $param_pass    = $hashed_password;
                        $param_contact = $contact_no;
                        
                        if (mysqli_stmt_execute($stmt_insert)) {
                            // Registration successful — redirect to login
                            $_SESSION['flash_success'] = "Registration successful! Please log in.";
                            header("Location: login.php");
                            exit();
                        } else {
                            $error = "Oops! Something went wrong. Please try again later.";
                        }
                    }
                    mysqli_stmt_close($stmt_insert);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Close connection
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Lost & Found Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
    <a class="brand" href="index.php">🔍 Lost &amp; <span>Found</span></a>
    <ul>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php" class="btn-nav">Register</a></li>
    </ul>
</nav>

<div class="container-sm">
    <div class="auth-card">
        <h2>📝 Create Account</h2>
        <p class="auth-sub">Join the Lost &amp; Found Portal</p>

        <?php if ($error != ""): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control"
                       placeholder="e.g. Rahul Sharma"
                       value="<?php echo htmlspecialchars(isset($_POST['name']) ? $_POST['name'] : ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="e.g. rahul@college.edu"
                       value="<?php echo htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="contact_no">Contact Number (10 digits)</label>
                <input type="tel" id="contact_no" name="contact_no" class="form-control"
                       placeholder="e.g. 9876543210"
                       value="<?php echo htmlspecialchars(isset($_POST['contact_no']) ? $_POST['contact_no'] : ''); ?>"
                       maxlength="10" required>
            </div>

            <div class="form-group">
                <label for="password">Password (min 6 characters)</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Create a strong password" required>
            </div>

            <div class="form-group">
                <label for="confirm_pw">Confirm Password</label>
                <input type="password" id="confirm_pw" name="confirm_pw" class="form-control"
                       placeholder="Re-enter your password" required>
            </div>

            <button type="submit" name="register" class="btn btn-primary btn-full">Create Account</button>
        </form>

        <div class="auth-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Lost &amp; Found Portal | PHP &amp; SQL Project
</footer>

</body>
</html>
