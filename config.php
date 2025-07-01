<?php
$conn =mysqli_connect("localhost","admin","redhat","login&signup");
if(!$conn){
    echo "connection failed".mysqli_connect_error();
}
?>