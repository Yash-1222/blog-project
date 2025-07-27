<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Info Table</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
session_start();
include "config.php";

if (!isset($_SESSION['user'])) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Please log in to view this page.</div></div>";
    exit;
}

$userid = mysqli_real_escape_string($conn, $_SESSION['id']);
$sql = "SELECT * FROM users WHERE id = '$userid'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $imagePath = !empty($row['profile_path']) && file_exists($row['profile_path']) 
        ? $row['profile_path'] 
        : '../uploads/default-avatar.png';
    
    $role = !empty($row['roles']) ? $row['roles'] : '';

    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Profile Image" class="rounded-circle mx-auto d-block mt-4" style="width:150px; height:150px; object-fit:cover;">';
    echo "<h1 class='text-center mt-3'>" . htmlspecialchars($role) . "</h1>";

    echo "<div class='container mt-4'>
            <table class='table table-bordered table-striped'>
                <thead class='table-light'>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Comment</th>
                        <th>Hobbies</th>
                        <th>Country</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['gender']) . "</td>
                        <td>" . htmlspecialchars($row['comment']) . "</td>
                        <td>" . htmlspecialchars($row['hobbies']) . "</td>
                        <td>" . htmlspecialchars($row['country']) . "</td>
                    </tr>
                </tbody>
            </table>
          </div>";
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>No user data found.</div></div>";
}

mysqli_close($conn);
?>

</body>
</html>
