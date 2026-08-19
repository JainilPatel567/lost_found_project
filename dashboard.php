<?php

require_once "includes/auth.php";
require_once "includes/config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login first");
    exit();
}
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : "All";
$sort   = isset($_GET['sort'])   ? trim($_GET['sort'])   : "newest";

$sql_stats = "SELECT
    SUM(post_type = 'Lost'  AND status = 'Active')   AS lost_count,
    SUM(post_type = 'Found' AND status = 'Active')   AS found_count,
    SUM(status = 'Resolved')                          AS resolved_count
FROM items";

$result_stats = mysqli_query($link, $sql_stats);
$stats = mysqli_fetch_assoc($result_stats);

$sql = "SELECT
            i.item_id,
            i.post_type,
            i.item_name,
            i.description,
            i.location,
            i.image_path,
            i.status,
            i.posted_at,
            u.name       AS poster_name,
            u.contact_no AS poster_contact,
            u.email      AS poster_email
        FROM items i
        INNER JOIN users u ON i.user_id = u.user_id
        WHERE i.status = 'Active'";


$types  = "";
$params = array();

if ($filter === "Lost" || $filter === "Found") {
    $sql      .= " AND i.post_type = ?";
    $types    .= "s";
    $params[]  = $filter;
}


if ($search != "") {
    $sql      .= " AND (i.item_name LIKE ? OR i.description LIKE ? OR i.location LIKE ?)";
    $types    .= "sss";
    $like      = "%" . $search . "%";
    $params[]  = $like;
    $params[]  = $like;
    $params[]  = $like;
}
if ($sort === "oldest") {
    $sql .= " ORDER BY i.posted_at ASC";
} else {
    $sql .= " ORDER BY i.posted_at DESC";
}
$items = array();
if ($stmt = mysqli_prepare($link, $sql)) {
    // Only bind parameters if we have any
    if (count($params) > 0) {
        // Build the argument list dynamically for bind_param
        $bind_args = array($stmt, $types);
        foreach ($params as $key => $val) {
            $bind_args[] = &$params[$key];
        }
        call_user_func_array('mysqli_stmt_bind_param', $bind_args);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
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
    <title>Dashboard | Lost & Found Portal</title>
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
    <div class="hero">
        <div>
            <h1>Welcome, <?php echo $_SESSION['user_name']; ?>! 👋</h1>
            <p>Browse all active lost &amp; found posts from your college community.</p>
        </div>
        <a href="post_item.php" class="btn btn-outline" style="color:#fff;border-color:#fff;">+ Post a New Item</a>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-num" style="color:var(--danger)">
                <?php echo intval($stats['lost_count']); ?></div>
            <div class="stat-label">Items Lost</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:var(--success)">
                <?php echo intval($stats['found_count']); ?></div>
            <div class="stat-label">Items Found</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:var(--warning)">
                <?php echo intval($stats['resolved_count']); ?></div>
            <div class="stat-label">Resolved</div>
        </div>
        <div class="stat-box">
            <div class="stat-num">
                <?php echo count($items); ?></div>
            <div class="stat-label">Showing</div>
        </div>
    </div>

    <h2 class="page-title">📋 Active Posts</h2>
    <p class="page-subtitle">Contact the poster directly to claim or return an item.</p>

    <!-- Filter / Search Bar -->
    <form method="GET" action="dashboard.php">
        <div class="filter-bar">
            <input type="text" name="search" placeholder="🔎 Search by name, location..."
                   value="<?php echo htmlspecialchars($search); ?>">
            <select name="filter">
                <option value="All"
                   <?php echo ($filter === "All"   ? "selected" : ""); ?>>All Types</option>
                <option value="Lost"  <?php echo ($filter === "Lost"  ? "selected" : ""); ?>>Lost Only</option>
                <option value="Found" <?php echo ($filter === "Found" ? "selected" : ""); ?>>Found Only</option>
            </select>
            <select name="sort">
                <option value="newest" <?php echo ($sort === "newest" ? "selected" : ""); ?>>Newest First</option>
                <option value="oldest" <?php echo ($sort === "oldest" ? "selected" : ""); ?>>Oldest First</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="dashboard.php" class="btn btn-outline">Reset</a>
        </div>
    </form>

    <!-- Items Grid -->
    <?php if (count($items) === 0): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>No active posts found matching your criteria.</p>
        </div>
    <?php else: ?>
        <div class="items-grid">
            <?php while ($item = array_shift($items)): ?>
                <div class="item-card">

                    <?php if ($item['image_path'] != "" && file_exists($item['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Item Image">
                    <?php endif; ?>

                    <div class="item-card-header">
                        <span class="badge badge-<?php echo strtolower($item['post_type']); ?>">
                            <?php echo htmlspecialchars($item['post_type']); ?>
                        </span>
                        <span class="badge badge-active">Active</span>
                    </div>

                    <div class="item-card-body">
                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                        <div class="item-desc">
                            <?php
                            // Show first 100 chars of description
                            $desc = $item['description'];
                            echo htmlspecialchars(strlen($desc) > 100 ? substr($desc, 0, 100) . "..." : $desc);
                            ?>
                        </div>
                        <div class="item-location"><?php echo htmlspecialchars($item['location']); ?></div>

                        <hr class="divider">

                        <!-- Contact info — fetched from users table via JOIN -->
                        <small style="color:var(--muted)">
                            <strong>Posted by:</strong> <?php echo htmlspecialchars($item['poster_name']); ?><br>
                            📞 <?php echo htmlspecialchars($item['poster_contact']); ?> &nbsp;|&nbsp;
                            ✉️ <?php echo htmlspecialchars($item['poster_email']); ?>
                        </small>
                    </div>

                    <div class="item-card-footer">
                        <span>🕒 <?php echo date('d M Y', strtotime($item['posted_at'])); ?></span>
                        <span>#<?php echo $item['item_id']; ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

</div>

<footer>
    &copy; <?php echo date('Y'); ?> Lost &amp; Found Portal | PHP &amp; SQL Project
</footer>

</body>
</html>
