<?php
include "config.php";

$search_item = isset($_POST['char']) ? $_POST['char'] : "";
$filter = isset($_POST['filter']) ? $_POST['filter'] : "recent";

$search_item = mysqli_real_escape_string($conn, $search_item);
$filter = mysqli_real_escape_string($conn, $filter);

$sql = "SELECT p.*, u.username FROM posts p 
        INNER JOIN users u ON p.user_id = u.id 
        WHERE p.title LIKE '%$search_item%'AND p.status = 'active'";

if ($filter === "author") {
    $sql .= " ORDER BY u.username ASC";
} else {
    $sql .= " ORDER BY p.created_at DESC";
}

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "
        <div class='blog-post'>
            <h2>Author : " . htmlspecialchars($row['username']) . "</h2>
            <h2><a href='viewpost.php?id=" . $row['id'] . "' class='blog-title-link'>Title : " . htmlspecialchars($row['title']) . "</a></h2>
            <p class='meta'>Posted on " . date('F j, Y', strtotime($row['created_at'])) . "</p>
            <p>" . nl2br(htmlspecialchars(substr($row['content'], 0, 100))) . "...</p>
        </div>";
    }
} else {
    echo "<h1 style='text-align:center'>No blog posts found.</h1>";
}

mysqli_close($conn);
?>
