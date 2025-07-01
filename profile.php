<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Info Table</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }

        th, td {
            padding: 10px;
            border: 1px solid #999;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .profile-img {
            display: block;
            margin: 30px auto 10px auto;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        h1
        {
            text-align:center;
        }
    </style>
</head>
<body>

<?php
session_start();
include "config.php";

if (!isset($_SESSION['user'])) {
    echo "<p>Please log in to view this page.</p>";
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
    $role =!empty($row['roles'])?$row['roles']:'';
    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Profile Image" class="profile-img">';
    echo "<h1>".htmlspecialchars($role)."</h1>";
    echo "<table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Comment</th>
            <th>Hobbies</th>
            <th>Country</th>
        </tr>";

    echo "<tr>
        <td>" . htmlspecialchars($row['username']) . "</td>
        <td>" . htmlspecialchars($row['email']) . "</td>
        <td>" . htmlspecialchars($row['gender']) . "</td>
        <td>" . htmlspecialchars($row['comment']) . "</td>
        <td>" . htmlspecialchars($row['hobbies']) . "</td>
        <td>" . htmlspecialchars($row['country']) . "</td>
    </tr>";

    echo "</table>";
} else {
    echo "<p>No user data found.</p>";
}

mysqli_close($conn);
?>

</body>
</html>
