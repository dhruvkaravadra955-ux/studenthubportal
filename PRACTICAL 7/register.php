<?php
// Empty variables (so form does not give error on first load)
$name = "";
$email = "";
$mobile = "";
$course = "";
$year = "";
$gender = "";
$errors = array();
$success = "";

// Function to clean the input (sanitization)
function clean($data) {
  $data = trim($data);              // remove extra spaces
  $data = stripslashes($data);      // remove backslashes
  $data = htmlspecialchars($data);  // stop HTML/script injection
  return $data;
}

// 1. Check that form is submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // 2. Take and sanitize inputs
  $name = clean($_POST["name"]);
  $email = clean($_POST["email"]);
  $mobile = clean($_POST["mobile"]);
  $password = $_POST["password"];
  $confirm = $_POST["confirm"];
  $course = clean($_POST["course"]);
  $year = clean($_POST["year"]);
  if (isset($_POST["gender"])) {
    $gender = clean($_POST["gender"]);
  }

  // 3. Server side validation
  if ($name == "") {
    $errors[] = "Name is required.";
  } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
    $errors[] = "Name should contain only letters and spaces.";
  }

  if ($email == "") {
    $errors[] = "Email is required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email format is not valid.";
  }

  if ($mobile == "") {
    $errors[] = "Mobile number is required.";
  } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must be exactly 10 digits.";
  }

  if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
  }

  if ($password != $confirm) {
    $errors[] = "Password and Confirm Password do not match.";
  }

  if ($course == "") {
    $errors[] = "Please select a course.";
  }

  if ($year == "") {
    $errors[] = "Please select a year.";
  }

  if ($gender == "") {
    $errors[] = "Please select gender.";
  }

  // 4. If no errors, save data in CSV file
  if (count($errors) == 0) {

    // make data folder if it does not exist
    if (!is_dir("data")) {
      mkdir("data");
    }

    $file = "data/students.csv";
    $newFile = !file_exists($file);

    $f = fopen($file, "a");   // "a" = append (add at the end)

    if ($f) {
      flock($f, LOCK_EX);     // lock file so two users do not write together

      // write heading only for the first time
      if ($newFile) {
        fputcsv($f, array("Name", "Email", "Mobile", "Course", "Year", "Gender", "Password", "Date"), ",", '"', "\\");
      }

      // never store plain password, so we hash it
      $hashed = password_hash($password, PASSWORD_DEFAULT);

      fputcsv($f, array($name, $email, $mobile, $course, $year, $gender, $hashed, date("d-m-Y H:i")), ",", '"', "\\");

      flock($f, LOCK_UN);
      fclose($f);

      $success = "Registration successful! Your data is saved.";

      // clear the form
      $name = $email = $mobile = $course = $year = $gender = "";
    } else {
      $errors[] = "Could not open the file. Data not saved.";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Student Registration</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <div class="container">
    <h1>Student Registration</h1>

    <!-- Success message -->
    <?php if ($success != "") { ?>
      <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <!-- Error messages -->
    <?php if (count($errors) > 0) { ?>
      <div class="error">
        <?php foreach ($errors as $e) { ?>
          <p><?php echo $e; ?></p>
        <?php } ?>
      </div>
    <?php } ?>

    <form method="POST" action="register.php">

      <label>Name:</label>
      <input type="text" name="name" value="<?php echo $name; ?>">

      <label>Email:</label>
      <input type="text" name="email" value="<?php echo $email; ?>">

      <label>Mobile Number:</label>
      <input type="text" name="mobile" value="<?php echo $mobile; ?>">

      <label>Password:</label>
      <input type="password" name="password">

      <label>Confirm Password:</label>
      <input type="password" name="confirm">

      <label>Course:</label>
      <select name="course">
        <option value="">Select Course</option>
        <option value="BCA" <?php if ($course == "BCA") echo "selected"; ?>>BCA</option>
        <option value="BBA" <?php if ($course == "BBA") echo "selected"; ?>>BBA</option>
        <option value="B.Com" <?php if ($course == "B.Com") echo "selected"; ?>>B.Com</option>
        <option value="B.Sc" <?php if ($course == "B.Sc") echo "selected"; ?>>B.Sc</option>
      </select>

      <label>Year:</label>
      <select name="year">
        <option value="">Select Year</option>
        <option value="1st Year" <?php if ($year == "1st Year") echo "selected"; ?>>1st Year</option>
        <option value="2nd Year" <?php if ($year == "2nd Year") echo "selected"; ?>>2nd Year</option>
        <option value="3rd Year" <?php if ($year == "3rd Year") echo "selected"; ?>>3rd Year</option>
      </select>

      <label>Gender:</label>
      <div class="gender">
        <input type="radio" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
        <input type="radio" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female
      </div>

      <button type="submit">Register</button>
    </form>

   
  </div>

</body>
</html>