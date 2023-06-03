<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
   header('location:login_form.php');
}

// Retrieve information about the logged-in user from the 'user_form' table
$loggedUser = $_SESSION['user_name'];
$userQuery = "SELECT * FROM user_form WHERE name = '$loggedUser'";
$resultUser = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_assoc($resultUser);

// Get the user ID
$userId = $userData['id'];

// Retrieve the courses from the 'courses' table
$sqlCourses = "SELECT * FROM courses";
$resultCourses = mysqli_query($conn, $sqlCourses);

// Handle the registration process
if (isset($_POST['register'])) {
   $courseId = $_POST['course_id'];

   // Check if the user is already registered for the selected course
   $checkQuery = "SELECT * FROM registered_courses WHERE user_id = '$userId' AND course_id = '$courseId'";
   $checkResult = mysqli_query($conn, $checkQuery);

   if (mysqli_num_rows($checkResult) == 0) {
      // Insert the registration data into the 'registered_courses' table
      $registerQuery = "INSERT INTO registered_courses (user_id, course_id) VALUES ('$userId', '$courseId')";
      mysqli_query($conn, $registerQuery);
   }
}

// Handle the deletion process
if (isset($_POST['delete'])) {
   $courseId = $_POST['course_id'];

   // Delete the course registration from the 'registered_courses' table
   $deleteQuery = "DELETE FROM registered_courses WHERE user_id = '$userId' AND course_id = '$courseId'";
   mysqli_query($conn, $deleteQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Page</title>
   <style>
      body {
         display: flex;
         justify-content: center;
         align-items: center;
         height: 100vh;
         background-color: #f7f9fc;
         font-family: Arial, sans-serif;
         margin: 0;
      }
      body {
         font-family: Arial, sans-serif;
         background-image: url('image2.jpg');
         background-repeat: no-repeat;
         background-size: cover;
      }

      .container {
         text-align: center;
         padding: 20px;
         border-radius: 8px;
         background-color: #fff;
         box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         max-width: 900px;
      }

      .header {
         margin-bottom: 20px;
      }

      h3 {
         font-size: 24px;
         color: #333;
      }

      h1 {
         font-size: 36px;
         color: #333;
      }

      p {
         font-size: 18px;
         color: #555;
         margin-bottom: 20px;
      }

      .table-container {
         display: inline-block;
         width: 48%;
         vertical-align: top;
         margin-top: 20px;
      }

      table {
         width: 100%;
         border-collapse: collapse;
      }

      th, td {
         padding: 12px;
         text-align: left;
         border-bottom: 1px solid #ddd;
      }

      th {
         background-color: #4caf50;
         color: white;
         font-weight: bold;
      }

      tr:nth-child(even) {
         background-color: #f2f2f2;
      }

      .btn-container {
         margin-top: 20px;
      }

      .btn {
         display: inline-block;
         padding: 10px 20px;
         font-size: 16px;
         font-weight: bold;
         background-color: #4CAF50;
         color: white;
         text-decoration: none;
         border-radius: 4px;
         transition: background-color 0.3s ease;
         margin: 5px;
      }

      .btn:hover {
         background-color: #45a049;
      }

      .btn-register {
         background-color: #4CAF50;
      }

      .btn-delete {
         background-color: #ff0000;
      }
   </style>
</head>
<body>

<div class="container">
   <div class="header">
      <h3>Hi, <span><?php echo $_SESSION['user_name'] ?></span></h3>
      <h1>Welcome, User</h1>
      <p>This is a user page</p>
   </div>

   <div class="table-container">
      <h3>All Courses</h3>
      <table>
         <thead>
            <tr>
               <th>Course Code</th>
               <th>Instructor Name</th>
               <th>Course Name</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <?php
               // Loop through each row in the result set and display the course details
               while ($row = mysqli_fetch_assoc($resultCourses)) {
                  echo "<tr>";
                  echo "<td>" . $row['course_id'] . "</td>";
                  echo "<td>" . $row['Instructor'] . "</td>";
                  echo "<td>" . $row['course_name'] . "</td>";
                  echo "<td>";
                  echo "<form method='post' action=''>";
                  echo "<input type='hidden' name='course_id' value='" . $row['course_id'] . "'>";
                  echo "<button class='btn btn-register' type='submit' name='register'>Register</button>";
                  echo "</form>";
                  echo "</td>";
                  echo "</tr>";
               }
            ?>
         </tbody>
      </table>
   </div>

   <div class="table-container">
      <h3>Registered Courses</h3>
      <table>
         <thead>
            <tr>
               <th>Course Code</th>
               <th>Instructor Name</th>
               <th>Course Name</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <?php
               $registeredCoursesQuery = "SELECT courses.course_id, courses.Instructor, courses.course_name 
                                          FROM courses 
                                          INNER JOIN registered_courses ON courses.course_id = registered_courses.course_id 
                                          WHERE registered_courses.user_id = '$userId'";
               $registeredCoursesResult = mysqli_query($conn, $registeredCoursesQuery);
               while ($registeredRow = mysqli_fetch_assoc($registeredCoursesResult)) {
                  echo "<tr>";
                  echo "<td>" . $registeredRow['course_id'] . "</td>";
                  echo "<td>" . $registeredRow['Instructor'] . "</td>";
                  echo "<td>" . $registeredRow['course_name'] . "</td>";
                  echo "<td>";
                  echo "<form method='post' action=''>";
                  echo "<input type='hidden' name='course_id' value='" . $registeredRow['course_id'] . "'>";
                  echo "<button class='btn btn-delete' type='submit' name='delete'>Delete</button>";
                  echo "</form>";
                  echo "</td>";
                  echo "</tr>";
               }
            ?>
         </tbody>
      </table>
   </div>

   <div class="btn-container">
      <a href="logout.php" class="btn">Logout</a>
   </div>
</div>

</body>
</html>
