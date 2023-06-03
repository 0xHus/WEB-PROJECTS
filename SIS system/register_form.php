<?php
@include 'config.php';

session_start();

// Check if the form is submitted
if (isset($_POST['submit'])) {
   // Verify CSRF token
   if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      die("CSRF token validation failed!");
   }

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);
   $user_type = $_POST['user_type'];

   $select = "SELECT * FROM user_form WHERE email = '$email' && password = '$pass'";
   $result = mysqli_query($conn, $select);

   if (mysqli_num_rows($result) > 0) {
      $error[] = 'User already exists!';
   } else {
      if ($pass != $cpass) {
         $error[] = 'Passwords do not match!';
      } else {
         $insert = "INSERT INTO user_form(name, email, password, user_type) VALUES('$name','$email','$pass','$user_type')";
         mysqli_query($conn, $insert);
         header('location:login_form.php');
      }
   }
}

// Generate and store CSRF token
if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Form</title>
   <style>
      body {
         display: flex;
         align-items: center;
         justify-content: center;
         height: 100vh;
         margin: 0;
         font-family: Arial, sans-serif;
      }
      body {
         font-family: Arial, sans-serif;
         background-image: url('image2.jpg');
         background-repeat: no-repeat;
         background-size: cover;
      }
      .form-container {
         text-align: center;
      }

      .error-msg {
         display: block;
         color: #ff0000;
         margin-bottom: 10px;
      }

      input[type="text"],
      input[type="email"],
      input[type="password"],
      select {
         width: 100%;
         padding: 10px;
         margin-bottom: 10px;
         border: 1px solid #ccc;
         border-radius: 4px;
         box-sizing: border-box;
      }

      input[type="submit"] {
         background-color: #4caf50;
         color: #ffffff;
         border: none;
         padding: 12px 20px;
         border-radius: 4px;
         cursor: pointer;
         width: 100%;
      }

      input[type="submit"]:hover {
         background-color: #45a049;
      }

      p {
         text-align: center;
         margin-top: 15px;
      }

      a {
         color: #4caf50;
      }
   </style>
</head>
<body>
   <div class="form-container">
      <form action="" method="post">
         <h3>Register Now</h3>
         <?php
         if(isset($error)){
            foreach($error as $error){
               echo '<span class="error-msg">'.$error.'</span>';
            };
         };
         ?>
         <input type="text" name="name" required placeholder="Enter your name">
         <input type="email" name="email" required placeholder="Enter your email">
         <input type="password" name="password" required placeholder="Enter your password">
         <input type="password" name="cpassword" required placeholder="Confirm your password">
         <select name="user_type">
            <option value="user">User</option>
            <option value="admin">Admin</option>
         </select>
         <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
         <input type="submit" name="submit" value="Register Now">
         <p>Already have an account? <a href="login_form.php">Login Now</a></p>
      </form>
   </div>
</body>
</html>
