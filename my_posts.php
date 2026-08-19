<?php


require_once "includes/auth.php";
require_once "includes/config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login first");
    exit();
}
$user_id = $_SESSION['user_id'];
$message  = "";
$msg_type = "success";


if (isset($_GET["action"]) && $_GET["action"] === "resolve" && isset($_GET["id"])) {
    $item_id = intval($_GET["id"]);


    $sql = "UPDATE items SET status = 'Resolved' WHERE item_id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $param_item_id, $param_user_id);
        $param_item_id  = $item_id;
        $param_user_id  = $user_id;
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "✅ Item marked as Resolved successfully!";
            } else {
                $message  = "Could not update. This item may not belong to you.";
                $msg_type = "error";
            }
        } else {
            $message  = "Oops! Something went wrong. Please try again later.";
            $msg_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

if (isset($_GET["action"]) && $_GET["action"] === "reopen" && isset($_GET["id"])) {
    $item_id = intval($_GET["id"]);

    $sql = "UPDATE items SET status = 'Active' WHERE item_id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $param_item_id, $param_user_id);
        $param_item_id = $item_id;
        $param_user_id = $user_id;
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "🔄 Item re-opened as Active!";
            } else {
                $message  = "Could not update item.";
                $msg_type = "error";
            }
        }
        mysqli_stmt_close($stmt);
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_item_id"])) {
    $item_id = intval($_POST["delete_item_id"]);
    $sql_img = "SELECT image_path FROM items WHERE item_id = ? AND user_id = ?";
    $img_path = NULL;
    if ($stmt_img = mysqli_prepare($link, $sql_img)) {
        mysqli_stmt_bind_param($stmt_img, "ii", $param_item_id, $param_user_id);
        $param_item_id = $item_id;
        $param_user_id = $user_id;
        if (mysqli_stmt_execute($stmt_img)) {
            $result_img = mysqli_stmt_get_result($stmt_img);
            $img_row    = mysqli_fetch_assoc($result_img);
            if ($img_row) {
                $img_path = $img_row["image_path"];
            }
        }
        mysqli_stmt_close($stmt_img);
    }

    $sql = "DELETE FROM items WHERE item_id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $param_item_id2, $param_user_id2);
        $param_item_id2 = $item_id;
        $param_user_id2 = $user_id;
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                
                if ($img_path != NULL && file_exists($img_path)) {
                    unlink($img_path);
                }
                $message = "🗑️ Post deleted successfully.";
            } else {
                $message  = "Could not delete. You may not own this post.";
                $msg_type = "error";
            }
        } else {
            $message  = "Oops! Something went wrong. Please try again later.";
            $msg_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

$my_items = array();
$sql = "SELECT item_id, post_type, item_name, description, location, image_path, status, posted_at
        FROM items
        WHERE user_id = ?
        ORDER BY posted_at DESC";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_user_id);
    $param_user_id = $user_id;
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $my_items[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts | Lost & Found Portal</title>
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

<div class="container">
    <h1 class="page-title">📂 My Posts</h1>
    <p class="page-subtitle">Manage all items you have posted. You can resolve or delete them.</p>

    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $msg_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <a href="post_item.php" class="btn btn-primary" style="margin-bottom:1.5rem;display:inline-block;">
        + Post New Item
    </a>

    <?php if (count($my_items) === 0): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>You haven't posted anything yet.</p>
            <a href="post_item.php" class="btn btn-primary" style="margin-top:1rem;">Post Your First Item</a>
        </div>
    <?php else: ?>
        <div class="items-grid">
            <?php foreach ($my_items as $item): ?>
                <div class="item-card">

                    <?php if ($item['image_path'] != "" && file_exists($item['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Item Image">
                    <?php endif; ?>

                    <div class="item-card-header">
                        <span class="badge badge-<?php echo strtolower($item['post_type']); ?>">
                            <?php echo htmlspecialchars($item['post_type']); ?>
                        </span>
                        <span class="badge badge-<?php echo strtolower($item['status']); ?>">
                            <?php echo htmlspecialchars($item['status']); ?>
                        </span>
                    </div>

                    <div class="item-card-body">
                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                        <div class="item-desc">
                            <?php
                            $desc = $item['description'];
                            echo htmlspecialchars(strlen($desc) > 100 ? substr($desc, 0, 100) . "..." : $desc);
                            ?>
                        </div>
                        <div class="item-location"><?php echo htmlspecialchars($item['location']); ?></div>
                    </div>

                    <div class="item-card-footer" style="flex-direction:column;align-items:flex-start;gap:0.5rem;">
                        <small>🕒 <?php echo date('d M Y, h:i A', strtotime($item['posted_at'])); ?></small>

                        <div class="post-actions">

                            <!-- UPDATE: Resolve button (shown only for Active posts) -->
                            <?php if ($item['status'] === "Active"): ?>
                                <a href="my_posts.php?action=resolve&id=<?php echo $item['item_id']; ?>"
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Mark this item as Resolved?')">
                                    ✅ Resolve
                                </a>
                            <?php else: ?>
                                <!-- UPDATE: Re-open button (shown for Resolved posts) -->
                                <a href="my_posts.php?action=reopen&id=<?php echo $item['item_id']; ?>"
                                   class="btn btn-outline btn-sm"
                                   onclick="return confirm('Re-open this post as Active?')">
                                    🔄 Re-Open
                                </a>
                            <?php endif; ?>

                            <!-- DELETE: Remove post permanently -->
                            <form method="POST" action="my_posts.php"
                                  onsubmit="return confirm('⚠️ Permanently delete this post?');"
                                  style="display:inline;">
                                <input type="hidden" name="delete_item_id" value="<?php echo $item['item_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<footer>
    &copy; <?php echo date('Y'); ?> Lost &amp; Found Portal | PHP &amp; SQL Project
</footer>

</body>
</html>
