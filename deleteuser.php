<?php
include "config.php";
$id = isset($_GET['id'])?$_GET['id']:"";
if(empty($id)){
    echo "id didn't get";
    exit;
}
else{
    $sql = "UPDATE users SET is_deleted = '1' WHERE id='$id'"; 
    $result= mysqli_query($conn,$sql);
    if($result){
        echo "deleted successfully";
        header('Refresh:3,URL=users.php');
    }
    else{
            echo "query unsuccessful";
        header('Refresh:3,URL=users.php');
    }
}

?>