<?php
@include 'config.php';

session_start();

// Check if the form is submitted
if (isset($_POST['submit'])) {
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = md5($_POST['password']);

   // Prepare and execute the query
   $select = "SELECT * FROM user_form WHERE email = ? AND password = ?";
   $stmt = mysqli_prepare($conn, $select);
   mysqli_stmt_bind_param($stmt, "ss", $email, $password);
   mysqli_stmt_execute($stmt);
   $result = mysqli_stmt_get_result($stmt);

   if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_array($result);
      if ($row['user_type'] == 'admin') {
         $_SESSION['admin_name'] = $row['name'];
         header('location:admin_page.php');
      } elseif ($row['user_type'] == 'user') {
         $_SESSION['user_name'] = $row['name'];
         header('location:user_page.php');
      }
   } else {
      $error = 'Incorrect email or password!';
   }
}

// Check if the password change form is submitted
if (isset($_POST['change_password'])) {
   // Verify CSRF token
   if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      die("CSRF token validation failed!");
   }

   $email = $_POST['email'];
   $old_password = md5($_POST['old_password']);
   $new_password = md5($_POST['new_password']);

   // Prepare and execute the query
   $select = "SELECT * FROM user_form WHERE email = ? AND password = ?";
   $stmt = mysqli_prepare($conn, $select);
   mysqli_stmt_bind_param($stmt, "ss", $email, $old_password);
   mysqli_stmt_execute($stmt);
   $result = mysqli_stmt_get_result($stmt);

   if (mysqli_num_rows($result) > 0) {
      $update = "UPDATE user_form SET password = ? WHERE email = ?";
      $stmt = mysqli_prepare($conn, $update);
      mysqli_stmt_bind_param($stmt, "ss", $new_password, $email);
      mysqli_stmt_execute($stmt);
      $success = 'Password changed successfully!';
   } else {
      $error = 'Incorrect email or old password!';
   }
}

// Generate and store CSRF token
if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html>
<head>
   <title>Student Information System</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         background-image: url('image2.jpg');
         background-repeat: no-repeat;
         background-size: cover;
      }

      .container {
         width: 300px;
         margin: 0 auto;
         padding: 20px;
         background-color: #ffffff;
         border-radius: 5px;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }

      .logo {
         text-align: center;
         margin-bottom: 20px;
      }

      .logo img {
         width: 200px; 
      }

      h1 {
         text-align: center;
         margin-bottom: 20px;
      }

      .error-msg {
         display: block;
         color: #ff0000;
         margin-bottom: 10px;
      }

      .success-msg {
         display: block;
         color: #008000;
         margin-bottom: 10px;
      }

      input[type="email"],
      input[type="password"] {
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
   <div class="container">
      <div class="logo">
         <img src="logo_and_name.png" alt="Logo">
      </div>
      <h1>Student Information System</h1>
      <form action="" method="post">
         <h3>Login Now</h3>
         <?php
         if (isset($error)) {
            echo '<span class="error-msg">' . $error . '</span>';
         }
         ?>
         <input type="email" name="email" required placeholder="Enter your email">
         <input type="password" name="password" required placeholder="Enter your password">
         <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
         <input type="submit" name="submit" value="Login Now">
         <p>Don't have an account? <a href="register_form.php">Register Now</a></p>
      </form>

      <hr>

      <form action="" method="post">
         <h3>Change Password</h3>
         <?php
         if (isset($_SESSION['user_name'])) {
            echo '<input type="hidden" name="email" value="' . $_SESSION['user_name'] . '">';
            if (isset($error)) {
               echo '<span class="error-msg">' . $error . '</span>';
            }
            if (isset($success)) {
               echo '<span class="success-msg">' . $success . '</span>';
            }
            echo '<input type="hidden" name="csrf_token" value="' . $csrf_token . '">';
            echo '<input type="password" name="old_password" required placeholder="Enter old password">';
            echo '<input type="password" name="new_password" required placeholder="Enter new password">';
            echo '<input type="submit" name="change_password" value="Change Password">';
         } else {
            echo '<input type="email" name="email" required placeholder="Enter your email">';
            echo '<input type="hidden" name="csrf_token" value="' . $csrf_token . '">';
            echo '<input type="password" name="old_password" required placeholder="Enter old password">';
            echo '<input type="password" name="new_password" required placeholder="Enter new password">';
            echo '<input type="submit" name="change_password" value="Change Password">';
         }
         ?>
      </form>
   </div>
</body>
</html>
