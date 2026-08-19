<?php

require_once "includes/auth.php";
require_once "includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login first");
    exit();
}
$error   = "";
$success = "";


if (isset($_POST["post_item"])) {

    
    $post_type   = trim($_POST["post_type"]);
    $item_name   = trim($_POST["item_name"]);
    $description = trim($_POST["description"]);
    $location    = trim($_POST["location"]);
   $user_id = $_SESSION['user_id'];

    if (empty($post_type) || empty($item_name) || empty($description) || empty($location)) {
        $error = "All fields are required.";
    } elseif ($post_type !== "Lost" && $post_type !== "Found") {
        $error = "Invalid post type selected.";
    } else {
        $image_path = NULL;  

        if (isset($_FILES["item_image"]) && $_FILES["item_image"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES["item_image"];

            if ($file["error"] !== UPLOAD_ERR_OK) {
                $error = "File upload error. Please try again.";
            } else {
                
                $allowed_types = array("image/jpeg", "image/png", "image/gif", "image/webp");
                $max_size      = 2 * 1024 * 1024;  // 2 MB limit

                $finfo     = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file["tmp_name"]);
                finfo_close($finfo);

                if (!in_array($mime_type, $allowed_types)) {
                    $error = "Only JPG, PNG, GIF, and WEBP images are allowed.";
                } elseif ($file["size"] > $max_size) {
                    $error = "Image must be smaller than 2 MB.";
                } else {
                   
                    $ext       = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                    $new_name  = "item_" . time() . "_" . $user_id . "." . $ext;
                    $upload_dir = "uploads/";
                    $dest_path  = $upload_dir . $new_name;

                   
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                   
                    if (!move_uploaded_file($file["tmp_name"], $dest_path)) {
                        $error = "Failed to save the uploaded file.";
                    } else {
                        $image_path = $dest_path;  // Save path to store in database
                    }
                }
            }
        }

        if ($error === "") {
            $sql = "INSERT INTO items (user_id, post_type, item_name, description, location, image_path)
                    VALUES (?, ?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($link, $sql)) {
                
                mysqli_stmt_bind_param($stmt, "isssss",
                    $param_user_id,
                    $param_post_type,
                    $param_item_name,
                    $param_description,
                    $param_location,
                    $param_image_path
                );

                // Set parameter values
                $param_user_id     = $user_id;
                $param_post_type   = $post_type;
                $param_item_name   = $item_name;
                $param_description = $description;
                $param_location    = $location;
                $param_image_path  = $image_path;

                // Execute
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Your item has been posted successfully!";
                    // Clear POST data so the form resets after success
                    $_POST = array();
                } else {
                    $error = "Oops! Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
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
    <title>Post Item | Lost & Found Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
    <a class="brand" href="dashboard.php">🔍 Lost &amp; <span>Found</span></a>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="post_item.php">+ Post Item</a></li>
        <li><a href="my_posts.php">My Posts</a></li>
        <li><a href="logout.php" class="btn-nav">Logout (<?php echo $_SESSION['user_name']; ?>)</a></li>
    </ul>
</nav>

<div class="container-sm" style="max-width:600px;">
    <h1 class="page-title">📦 Post an Item</h1>
    <p class="page-subtitle">Fill in the details of the lost or found item.</p>

    <?php if ($success != ""): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
            <a href="dashboard.php">← View on Dashboard</a>
        </div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- enctype="multipart/form-data" is REQUIRED for file uploads -->
    <form method="POST" action="post_item.php" enctype="multipart/form-data">
        <div class="card">

            <div class="form-group">
                <label for="post_type">Post Type *</label>
                <select id="post_type" name="post_type" class="form-control" required>
                    <option value="">-- Select --</option>
                    <option value="Lost"  <?php echo (isset($_POST['post_type']) && $_POST['post_type'] === "Lost"  ? "selected" : ""); ?>>
                        🔴 Lost (I lost this item)
                    </option>
                    <option value="Found" <?php echo (isset($_POST['post_type']) && $_POST['post_type'] === "Found" ? "selected" : ""); ?>>
                        🟢 Found (I found this item)
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="item_name">Item Name *</label>
                <input type="text" id="item_name" name="item_name" class="form-control"
                       placeholder="e.g. Blue Water Bottle, Scientific Calculator"
                       value="<?php echo htmlspecialchars(isset($_POST['item_name']) ? $_POST['item_name'] : ''); ?>"
                       maxlength="150" required>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" class="form-control"
                          placeholder="Describe the item (color, brand, markings, etc.)"
                          required><?php echo htmlspecialchars(isset($_POST['description']) ? $_POST['description'] : ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" class="form-control"
                       placeholder="e.g. Library, Canteen, Lab 3"
                       value="<?php echo htmlspecialchars(isset($_POST['location']) ? $_POST['location'] : ''); ?>"
                       maxlength="200" required>
            </div>

            <!-- File Upload — optional -->
            <div class="form-group">
                <label for="item_image">Upload Image (optional — JPG/PNG, max 2MB)</label>
                <!-- MAX_FILE_SIZE hidden field hints the browser about size limit -->
                <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                <input type="file" id="item_image" name="item_image"
                       class="form-control" accept="image/*">
                <small style="color:var(--muted)">Accepted: JPG, PNG, GIF, WEBP</small>
            </div>

            <button type="submit" name="post_item" class="btn btn-primary btn-full">📤 Post Item</button>

        </div>
    </form>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Lost &amp; Found Portal | PHP &amp; SQL Project
</footer>

</body>
</html>
