<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    session_start();
    include "config.php";

    if(isset($_SESSION['id'])){
        $sql="UPDATE users SET remember_token=NULL WHERE id=?";
        $stmt=mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,'i',$_SESSION['id']);
        mysqli_stmt_execute($stmt);
    }

    $_SESSION=[]; // clear session variables
    
    if(ini_get("session.use_cookies")){
        $params =session_get_cookie_params();
        setcookie(session_name(),'',time()-42000,
        $params['path'],$params['domain'],
        $params['secure'],$params['httponly']
    );
    }

    setcookie('remember','',time()-3600,'/','',true,true);

    session_destroy();
    header('Location:login.php');
    exit;
    ?>
</body>
</html>