<?php
include 'src/config/database.php';

$errors = [];

// Fetch all departments
$query = "SELECT department_office, sector_category FROM department";
$result = $conn->query($query);
$departments = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
    $department_office = $_POST['department_office'];
    $password = $_POST['password'];
    $role = 'author';  
    $status = 'active';       

    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($department_office)) {
        $errors[] = "Department/Office is required.";
    }
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.";
    }

    if (empty($errors)) {
        // Check if the sector_category exists for the selected department
        $sector_query = "SELECT sector_category FROM department WHERE department_office = ?";
        $sector_stmt = $conn->prepare($sector_query);
        $sector_stmt->bind_param("s", $department_office);
        $sector_stmt->execute();
        $sector_result = $sector_stmt->get_result();

        if ($sector_result->num_rows > 0) {
            $sector = $sector_result->fetch_assoc()['sector_category'];

            // Insert into tbl_user
            $query = "INSERT INTO tbl_user (name, username, department_office, password, role, status, sector_category) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $name, $username, $department_office, $password, $role, $status, $sector);

            if ($stmt->execute()) {
                header("location:router.php?page=login");
                exit();
            } else {
                $errors[] = "Error registering user. Please try again.";
            }
        } else {
            $errors[] = "Selected department has no associated sector category.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
    <div class="container">
        <div class="form-wrapper">
            <div class="form-col">
                <img src="public/images/login.jpeg" alt="">
            </div>
            <div class="form-col log-form">
                <div class="log-form-header">
                    <h1>Template</h1>
                </div>

                <!-- Display errors if any -->
                <?php if (!empty($errors)): ?>
                    <div style="color: red; font-size: 14px; margin-bottom: 10px;">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form  method="POST">
                    <h1>Sign Up</h1>
                    <h5>Welcome to Template</h5>

                    <input type="hidden" name="role" value="author">
                    <input type="hidden" name="status" value="active">

                    <input type="text" name="name" placeholder="Enter full name" required>
                    <input type="text" name="username" placeholder="Enter username" required>
                    
                    <select name="department_office" required>
                        <option value="" disabled selected>Select Department/Office</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo htmlspecialchars($department['department_office']); ?>">
                                <?php echo htmlspecialchars($department['department_office']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="password" name="password" id="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                           title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" 
                           placeholder="Password" required>

                    <div id="message">
                        <h3>Password must contain the following:</h3>
                        <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                        <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                        <p id="number" class="invalid">A <b>number</b></p>
                        <p id="length" class="invalid">Minimum <b>8 characters</b></p>
                    </div>

                    <button type="submit">Sign up</button>
                    <p class="sign-up">Already have an account?<a href="router.php?page=login">Login!</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="public/js/form-validation.js"></script>
</html>
