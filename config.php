<?php
$conn =mysqli_connect("localhost","root","","login&signup");
if(!$conn){
    echo "connection failed".mysqli_connect_error();
}
?>