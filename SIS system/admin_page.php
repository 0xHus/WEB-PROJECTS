<?php
session_start();

@include 'config.php';

if (!isset($_SESSION['admin_name'])) {
   header('location:login_form.php');
}

// Delete course if course ID is provided
if (isset($_GET['delete_course']) && !empty($_GET['delete_course'])) {
   $courseId = $_GET['delete_course'];

   // Delete the course from the 'courses' table
   $deleteQuery = "DELETE FROM courses WHERE course_id = '$courseId'";
   mysqli_query($conn, $deleteQuery);

   header('location:admin_page.php');
   exit;
}

// Delete user if user ID is provided
if (isset($_GET['delete_user']) && !empty($_GET['delete_user'])) {
   $userId = $_GET['delete_user'];

   // Delete the user from the 'user_form' table
   $deleteQuery = "DELETE FROM user_form WHERE id = '$userId'";
   mysqli_query($conn, $deleteQuery);

   header('location:admin_page.php');
   exit;
}

if (isset($_POST['submit_course'])) {
   $courseCode = $_POST['course_code'];
   $instructorName = $_POST['instructor_name'];
   $courseName = $_POST['course_name'];

   // Insert the course details into the 'courses' table
   $sql = "INSERT INTO courses (course_id, instructor, course_name) VALUES ('$courseCode', '$instructorName', '$courseName')";

   if (mysqli_query($conn, $sql)) {
      echo "<script>alert('Course details added successfully');</script>";
   }
}

// Retrieve all the courses from the 'courses' table
$courseQuery = "SELECT * FROM courses";
$courseResult = mysqli_query($conn, $courseQuery);

// Retrieve all the users from the 'user_form' table
$userQuery = "SELECT * FROM user_form";
$userResult = mysqli_query($conn, $userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>
   <style>
      body {
         display: flex;
         justify-content: center;
         align-items: center;
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
      .container {
         text-align: center;
      }

      .content {
         margin-bottom: 20px;
      }

      .btn {
         display: inline-block;
         margin: 5px;
         padding: 10px 20px;
         background-color: #4caf50;
         color: #ffffff;
         text-decoration: none;
         border-radius: 4px;
         border: none;
         transition: background-color 0.3s ease;
      }

      .btn:hover {
         background-color: #45a049;
      }

      .cancel-btn {
         display: inline-block;
         margin-top: 10px;
         padding: 8px 16px;
         background-color: #f44336;
         color: #ffffff;
         text-decoration: none;
         border-radius: 4px;
         border: none;
         transition: background-color 0.3s ease;
      }

      .cancel-btn:hover {
         background-color: #d32f2f;
      }

      .form-group {
         margin-bottom: 10px;
      }

      .form-label {
         display: block;
         text-align: left;
         font-weight: bold;
         margin-bottom: 5px;
      }

      .form-input {
         width: 100%;
         padding: 8px;
         border-radius: 4px;
         border: 1px solid #ccc;
         box-sizing: border-box;
      }

      .add-course-btn {
         display: inline-block;
         margin-top: 10px;
         padding: 10px 20px;
         background-color: #4caf50;
         color: #ffffff;
         text-decoration: none;
         border-radius: 4px;
         border: none;
         transition: background-color 0.3s ease;
         font-size: 16px;
         font-weight: bold;
      }

      .add-course-btn:hover {
         background-color: #45a049;
      }

      .delete-course-btn,
      .delete-user-btn {
         display: inline-block;
         margin: 5px;
         padding: 8px 16px;
         background-color: #f44336;
         color: #ffffff;
         text-decoration: none;
         border-radius: 4px;
         border: none;
         transition: background-color 0.3s ease;
      }

      .delete-course-btn:hover,
      .delete-user-btn:hover {
         background-color: #d32f2f;
      }

      .table-container {
         display: flex;
         justify-content: space-between;
      }

      .table-container table {
         width: 45%;
         border-collapse: collapse;
         margin-top: 20px;
      }

      .table-container th,
      .table-container td {
         padding: 8px;
         text-align: left;
         border-bottom: 1px solid #ddd;
      }

      .table-container th {
         background-color: #4caf50;
         color: white;
      }

      .table-container tr:nth-child(even) {
         background-color: #f2f2f2;
      }
   </style>
   <script>
      function showSuccessMessage() {
         alert("Course details added successfully");
      }

      function confirmDelete() {
         return confirm("Are you sure you want to delete this?");
      }
   </script>
</head>
<body>
   <div class="container">
      <div class="content">
         <h3>Hi, <span>admin</span></h3>
         <h1>Welcome <span><?php echo $_SESSION['admin_name'] ?></span></h1>
         <p>This is an admin page</p>

         <form method="POST" onsubmit="showSuccessMessage();">
            <div class="form-group">
               <label for="course_code" class="form-label">Course Code:</label>
               <input type="text" id="course_code" name="course_code" class="form-input" required>
            </div>

            <div class="form-group">
               <label for="instructor_name" class="form-label">Instructor Name:</label>
               <input type="text" id="instructor_name" name="instructor_name" class="form-input" required>
            </div>

            <div class="form-group">
               <label for="course_name" class="form-label">Course Name:</label>
               <input type="text" id="course_name" name="course_name" class="form-input" required>
            </div>

            <input type="submit" name="submit_course" value="Add Course" class="add-course-btn">
         </form>

         <a href="logout.php" class="cancel-btn">Logout</a>

         <div class="table-container">
            <!-- Table to display courses -->
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
                     // Loop through each row in the course result set and display the course details
                     while ($row = mysqli_fetch_assoc($courseResult)) {
                        echo "<tr>";
                        echo "<td>".$row['course_id']."</td>";
                        echo "<td>".$row['Instructor']."</td>";
                        echo "<td>".$row['course_name']."</td>";
                        echo "<td><a href='admin_page.php?delete_course=".$row['course_id']."' class='delete-course-btn' onclick='return confirmDelete()'>Delete</a></td>";
                        echo "</tr>";
                     }
                  ?>
               </tbody>
            </table>

            <!-- Table to display users -->
            <table>
               <thead>
                  <tr>
                     <th>ID</th>
                     <th>Name</th>
                     <th>Email</th>
                     <th>User Type</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     // Loop through each row in the user result set and display the user details
                     while ($row = mysqli_fetch_assoc($userResult)) {
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td>".$row['email']."</td>";
                        echo "<td>".$row['user_type']."</td>";
                        echo "<td><a href='admin_page.php?delete_user=".$row['id']."' class='delete-user-btn' onclick='return confirmDelete()'>Delete</a></td>";
                        echo "</tr>";
                     }
                  ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</body>
</html>
