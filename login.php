<?php

require_once "includes/auth.php"; 
require_once "includes/config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

$flash = "";
if (isset($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

$url_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "";

if (isset($_POST["login"])) {


    $email       = trim($_POST["email"]);
    $password    = trim($_POST["password"]);
    $remember_me = isset($_POST["remember_me"]);
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $sql = "SELECT user_id, name, email, password FROM users WHERE email = ? LIMIT 1";
        if ($stmt = mysqli_prepare($link, $sql)) {
           
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            // Set parameter value
            $param_email = $email;
            // Execute
            if (mysqli_stmt_execute($stmt)) {
                // Get result set
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) === 1) {
                    $row = mysqli_fetch_assoc($result);

                    
                    if (password_verify($password, $row['password'])) {

                    
                        session_regenerate_id(true);

                      
                        $_SESSION['user_id']      = $row['user_id'];
                        $_SESSION['user_email']   = $row['email'];
                        $_SESSION['user_name']    = $row['name'];
                        $_SESSION['logged_in_at'] = time();

                        
                        
                        $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : "dashboard.php";
                        unset($_SESSION['redirect_after_login']);
                        header("Location: " . $redirect);
                        exit();

                    } else {
                        $error = "Incorrect password. Please try again.";
                    }
                } else {
                    $error = "No account found with that email. Please register.";
                }
            } else {
                $error = "Oops! Something went wrong. Please try again later.";
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
    <title>Login | Lost & Found Portal</title>
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
        <h2>🔐 Login</h2>
        <p class="auth-sub">Access your Lost &amp; Found account</p>

        <?php if ($flash != ""): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div>
        <?php endif; ?>

        <?php if ($url_msg != ""): ?>
            <div class="alert alert-info"><?php echo $url_msg; ?></div>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="Enter your registered email"
                       value="<?php echo htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label class="checkbox-row">
                    <input type="checkbox" name="remember_me" value="1">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-full">Login</button>
        </form>

        <div class="auth-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Lost &amp; Found Portal | PHP &amp; SQL Project
</footer>

</body>
</html>
