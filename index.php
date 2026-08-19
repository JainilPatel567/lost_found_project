<?php

require_once "includes/auth.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Portal | Welcome</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .landing-hero {
            min-height: calc(100vh - 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #0ea5e9 100%);
            text-align: center;
            color: #fff;
            padding: 2rem;
        }
        .landing-content { max-width: 600px; }
        .landing-content h1 { font-size: 3rem; font-weight: 900; margin-bottom: 1rem; }
        .landing-content p  { font-size: 1.1rem; opacity: 0.88; margin-bottom: 2rem; }
        .landing-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn-lp { padding: 0.85rem 2.5rem; border-radius: 30px; font-size: 1rem; font-weight: 700; }
        .btn-lp-white { background:#fff; color:#1e40af; }
        .btn-lp-outline { background:transparent; color:#fff; border:2px solid #fff; }
        .features { display:flex; gap:1.5rem; flex-wrap:wrap; justify-content:center; padding:3rem 2rem; }
        .feature-card { background:#fff; border-radius:12px; padding:2rem 1.5rem; text-align:center;
                        flex:1; min-width:180px; max-width:230px; box-shadow:0 2px 12px rgba(0,0,0,.07); }
        .feature-icon { font-size:2.5rem; margin-bottom:.8rem; }
    </style>
</head>
<body>

<nav>
    <a class="brand" href="index.php">🔍 Lost &amp; <span>Found</span></a>
    <ul>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php" class="btn-nav">Register</a></li>
    </ul>
</nav>

<div class="landing-hero">
    <div class="landing-content">
        <h1>🔍 Lost &amp; Found Portal</h1>
        <p>Report lost items and claim found ones within your college community.</p>
        <div class="landing-btns">
            <a href="register.php" class="btn-lp btn-lp-white">Get Started →</a>
            <a href="login.php"    class="btn-lp btn-lp-outline">Login</a>
        </div>
    </div>
</div>

<div class="features">
    <div class="feature-card">
        <div class="feature-icon">📢</div>
        <h3>Post Lost Items</h3>
        <p>Report any item you've lost with a photo and description.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🎁</div>
        <h3>Post Found Items</h3>
        <p>Found something? Help others by posting it here.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🔎</div>
        <h3>Search &amp; Filter</h3>
        <p>Find items by name, location, or type quickly.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">✅</div>
        <h3>Mark as Resolved</h3>
        <p>Close a post once the item is returned.</p>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Lost &amp; Found Portal | PHP &amp; SQL Project
</footer>

</body>
</html>
