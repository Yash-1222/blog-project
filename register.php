<?php
        include "config.php";
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        session_start(); 

        $name=$email=$pwd=$text=$gender=$country=$img=$file=$role="";
        $nameerr=$emailerr=$pwderr=$texterr=$gendererr=$countryerr=$imgerr=$hobbieerr=$fileerr=$roleerr="";
            $allowedhobbies=['Reading','Traveling','Gaming'];
            $hobbies= [];

          function test_input($data){
            $data= trim($data);
            $data= stripslashes($data);
            $data= htmlspecialchars($data);
            return $data;
        }

        if($_SERVER['REQUEST_METHOD']==="POST"){
            $name =isset($_POST['name'])?test_input($_POST['name']):'';
            $email =isset($_POST['mail'])?test_input($_POST['mail']):'';
            $pwd =isset($_POST['pwd'])?test_input($_POST['pwd']):'';
            $text =isset($_POST['comments'])?test_input($_POST['comments']):'';

            $has_error=false;
        // if(!$name||!$email||!$pwd){
        //     echo "<div class='text-danger'>All fields are required</div>";
        //     $has_error=true;
        // }
        if(empty($name)){
            $nameerr="Name is required";
              $has_error=true;
        }
        else if(!preg_match('/^[a-zA-Z ]+$/', $name)) {
            $nameerr = "Only alphabets and spaces are allowed";
             $has_error=true;
        }
      
        if(empty($email)){
            $emailerr="Gmail is required";
              $has_error=true;
        }
        else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $emailerr="Email is not valid";
             $has_error=true;
        }
         else{
            $checkemail ="SELECT * FROM users WHERE email='$email'";
            $check_query =mysqli_query($conn,$checkemail);
            if(mysqli_num_rows($check_query)>0){
                $emailerr="Email Already exist please login";
                $has_error=true;
            }
        }
          $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if(empty($pwd)){
            $pwderr="password is required";
            $has_error=true;
        }
        else if(!preg_match($pattern,$pwd)){
            $pwderr="passward must have atleast 8 character cantaining atleast one uppercase ,lowercase,number,special  character ";
            $has_error=true;
        }
        
        if(empty($text)){
            $texterr="textarea must have content ";
            $has_error=true;
        }
      
        $gender = isset($_POST['gender']) ? test_input($_POST['gender']) : '';
        if(empty($gender)){
            $gendererr="gender is required";
            $has_error=true;
        }


        if (isset($_POST['hobbies']) && is_array($_POST['hobbies']) && count($_POST['hobbies']) > 0) {
            $hobbies = array_map('test_input', $_POST['hobbies']);
            $hobbies_sting = implode(',',$hobbies);
        } else {
            $hobbieerr = "Please select at least one hobby";
            $has_error = true;
        }

        

       $country = isset($_POST['country']) ? $_POST['country'] : '';

        if (empty($country)) {
            $countryerr = "Please select at least one country";
            $has_error = true;
        }
        
          $role = isset($_POST['role']) ? $_POST['role'] : '';

        if (empty($role)) {
            $roleerr = "Please select at least one role";
            $has_error = true;
        }
        


        if(isset($_FILES['myfile'])){
            $file = $_FILES['myfile'];
            if($file['error']==0){
                $allowed_ext = array("jpg" => "image/jpg",
                               "jpeg" => "image/jpeg",
                               "gif" => "image/gif",
                               "png" => "image/png");
                $filename=basename($file['name']);
                $upload_dir="uploads/";
                $filepath= $upload_dir.$filename;
                $filesize =$file['size'];
                $filetype =$file['type'];
                $ext =strtolower(pathinfo($filename,PATHINFO_EXTENSION));

                if(!array_key_exists($ext,$allowed_ext)){
                    $fileerr="error :Please select valid file format";
                    $has_error=true;
                }
                $size = (2*1024*1024);

                if($filesize>$size){
                    $fileerr="File Size is larger than limit";
                    $has_error=true;
                }

                if(in_array($filetype,$allowed_ext)){
                    if(file_exists($filepath)){
                        $fileerr="$filename already exist";
                        $has_error=true;
                    }
                    else{
                        if(!move_uploaded_file($file['tmp_name'],$filepath)){
                          $fileerr="file can't uploaded";
                          $has_error=true;  
                        } 
                        else{
                            echo "uploaded successfully";
                        }          
                    }

                } else{
                    $fileerr="please try again";
                    $has_error=true;
                }
                
            }
        }else{
            $fileerr="please select file";
            $has_error=true;
        }

        if(!$has_error){
            $status ="active";
            $pass =password_hash($pwd,PASSWORD_DEFAULT);
             $sql="INSERT INTO users(username,email,password_hash,comment,gender,hobbies,country,profile_name,profile_path,roles,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                $stmt=mysqli_prepare($conn,$sql);
                if(!$stmt){
                    echo "query failed".mysqli_error($conn);
                    exit;
                }
                if(!mysqli_stmt_bind_param($stmt,"sssssssssss",$name,$email,$pass,$text,$gender,$hobbies_sting,$country,$filename,$filepath,$role,$status)){
                    echo "binding failed".'('.mysqli_stmt_errno($stmt).')'.mysqli_stmt_error($stmt);
                    exit;
                }
                if(!mysqli_stmt_execute($stmt)){
                    echo "query failed".'('.mysqli_stmt_errno($stmt).')'.mysqli_stmt_error($stmt);
                }
               else {
                    $_SESSION['success_msg'] = "Registration successful. Please login.";
                    header('Location: login.php');
                    exit;
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            }
        }
      
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Insert</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">User Registration Form</h3>
        </div>
        <div class="card-body">
          <form action="" method="post" enctype="multipart/form-data">

            <!-- Roles -->
            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="role" name="role">
                <option value="" disabled selected>Select your role</option>
                <option value="admin" <?= ($role=='admin') ? 'selected' : '' ?>>Admin</option>
                <option value="editor" <?= ($role=='editor') ? 'selected' : '' ?>>Editor</option>
                <option value="viewer" <?= ($role=='viewer') ? 'selected' : '' ?>>Viewer</option>
              </select>
              <div class="text-danger"><?= $roleerr ?></div>
            </div>

            <!-- Name -->
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>">
              <div class="text-danger"><?= $nameerr ?></div>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label for="mail" class="form-label">Email</label>
              <input type="email" class="form-control" id="mail" name="mail" value="<?= htmlspecialchars($email) ?>">
              <div class="text-danger"><?= $emailerr ?></div>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label for="pwd" class="form-label">Password</label>
              <input type="password" class="form-control" id="pwd" name="pwd">
              <div class="text-danger"><?= $pwderr ?></div>
            </div>

            <!-- Comments -->
            <div class="mb-3">
              <label for="comments" class="form-label">Comments</label>
              <textarea class="form-control" id="comments" name="comments" rows="3"><?= htmlspecialchars($text) ?></textarea>
              <div class="text-danger"><?= $texterr ?></div>
            </div>

            <!-- Gender -->
            <div class="mb-3">
              <label class="form-label d-block">Gender</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="male" name="gender" value="male" <?= ($gender=="male") ? "checked" : "" ?>>
                <label class="form-check-label" for="male">Male</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="female" name="gender" value="female" <?= ($gender=="female") ? "checked" : "" ?>>
                <label class="form-check-label" for="female">Female</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="other" name="gender" value="other" <?= ($gender=="other") ? "checked" : "" ?>>
                <label class="form-check-label" for="other">Other</label>
              </div>
              <div class="text-danger"><?= $gendererr ?></div>
            </div>

            <!-- Hobbies -->
            <div class="mb-3">
              <label class="form-label d-block">Hobbies</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="reading" name="hobbies[]" value="Reading" <?= in_array("Reading", $hobbies) ? 'checked' : '' ?>>
                <label class="form-check-label" for="reading">Reading</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="traveling" name="hobbies[]" value="Traveling" <?= in_array("Traveling", $hobbies) ? 'checked' : '' ?>>
                <label class="form-check-label" for="traveling">Traveling</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="gaming" name="hobbies[]" value="Gaming" <?= in_array("Gaming", $hobbies) ? 'checked' : '' ?>>
                <label class="form-check-label" for="gaming">Gaming</label>
              </div>
              <div class="text-danger"><?= $hobbieerr ?></div>
            </div>

            <!-- Country -->
            <div class="mb-3">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" id="country" name="country">
                <option value="" disabled selected>Select your country</option>
                <option value="United_States" <?= ($country=='United_States') ? "selected" : "" ?>>United States</option>
                <option value="United_Kingdom" <?= ($country=='United_Kingdom') ? "selected" : "" ?>>United Kingdom</option>
                <option value="Canada" <?= ($country=='Canada') ? "selected" : "" ?>>Canada</option>
                <option value="Australia" <?= ($country=='Australia') ? "selected" : "" ?>>Australia</option>
                <option value="other" <?= ($country=='Other') ? "selected" : "" ?>>Other</option>
              </select>
              <div class="text-danger"><?= $countryerr ?></div>
            </div>

            <!-- File Upload -->
            <div class="mb-3">
              <label for="myfile" class="form-label">Profile Image</label>
              <input class="form-control" type="file" name="myfile" id="myfile">
              <div class="text-danger"><?= $fileerr ?></div>
            </div>

            <!-- Submit -->
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a href="login.php" class="btn btn-outline-success">Already have an account? Login</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
