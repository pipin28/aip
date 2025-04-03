<?php
include 'src/config/database.php';
session_start();
//File for User Management View

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in the session
$role = $_SESSION['role'];  // Assuming status is stored in the session

// Redirect to login page if the user is not logged in
if (!isset($user_id) || !isset($role)) {
    header("Location: router.php?page=login");
    exit;
}




$errors = [];

// Fetch all departments
$query = "SELECT department_office, sector_category FROM department WHERE sector_category IS NOT NULL AND sector_category != ''";

$result = $conn->query($query);
$departments = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
    $department_office = $_POST['department_office'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];     

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
                header("location:router.php?page=user_management");
                exit();
            } else {
                $errors[] = "Error registering user. Please try again.";
            }
        } else {
            $errors[] = "Selected department has no associated sector category.";
        }
    }
}


if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
    $user_query = "SELECT * FROM tbl_user WHERE role = 'admin' ORDER by id DESC LIMIT 4";

} else {
    $user_query = "SELECT * FROM tbl_user WHERE role !='admin' ORDER by id DESC LIMIT 4";
}

$user_result = mysqli_query($conn, $user_query);

if ($_SESSION['role'] === 'admin') {
    $user_author_query = "SELECT * FROM tbl_user WHERE role = 'author' ORDER by id DESC LIMIT 4";

} else {
    $user_author_query = "SELECT * FROM tbl_user WHERE role ='author' ORDER by id DESC LIMIT 4";
}

$user_author_result = mysqli_query($conn, $user_author_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $department_office = $_POST['department_office'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $query = "UPDATE tbl_user SET name = ?, username = ?, department_office = ?, role = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $name, $username, $department_office, $role, $status, $id);

    if ($stmt->execute()) {
        header("location:router.php?page=user_management");
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM tbl_user WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        header("Location: router.php?page=user_management");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}


?>

<script>
    // ERROR TIMEOUT
    setTimeout(function() {
        var messageDiv = document.getElementById('error');
        if (messageDiv) {
            messageDiv.style.display = 'none';
        }
    }, 7000);
</script>

<?php include 'public/components/header.php'; ?>
<div class="hero">
    <div class="sector_header">
        <h1>USER MANAGEMENT</h1>
    </div>
    <div class="user_add_btn">
        <button onclick="document.getElementById('id01').style.display='block'"><i class="fa-solid fa-plus"></i> Add new</button>

        <button onclick="window.location.href='src/views/user_logins.php'" style="margin-left: 10px;">
    <i class="fa-solid fa-file-alt"></i> View Logs
</button>

    </div>
<div class="user_table_wrapper">
    <div class="user_table_col">
        <div class="table">
            <h4>Admin List</h4>
            <table class="user_table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($user_result) > 0): ?>
                        <?php while ($user = mysqli_fetch_assoc($user_result)): ?>
                            <tr>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['department_office'] ?></td>
                                <td><?= $user['role'] ?></td>
                                <td class="active-status user_status">
                                    <span class="status-text"><?= $user['status'] ?></span>
                                    <?php
                                    if ($user['status'] == 'active') {
                                        echo '<span class="status active"></span>';
                                    } else {
                                        echo '<span class="status in-active"></span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="user_action_btn">
                                        <a onclick="openEditModal(<?= $user['id'] ?>, '<?= $user['name'] ?>', '<?= $user['department_office'] ?>', '<?= $user['role'] ?>', '<?= $user['status'] ?>')" class="user_edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="router.php?page=user_management&delete_id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="user_delete"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No data found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
        <div class="user_table_col">
            <div class="table">
            <h4>User list</h4>
                <table class="user_table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department Office</th>
                            <th>Sector</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>

                        </tr>
                    </thead>
                    <?php if (mysqli_num_rows($user_author_result) > 0): ?>
                    <tbody>
                        <?php while($author = mysqli_fetch_assoc($user_author_result)):?>
                            <tr>
                                <td><?= $author['name'] ?></td>
                                <td><?= $author['department_office'] ?></td>
                                <td><?= $author['sector_category'] ?></td>
                                <td><?= $author['role'] ?></td>
                                <td class="active-status user_status">
                                    <span class="status-text"><?= $author['status'] ?></span>
                                    <?php
                                    if ($author['status'] == 'active') {
                                        echo '<span class="status active"></span>';
                                    } else {
                                        echo '<span class="status in-active"></span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="user_action_btn">
                                    <a onclick="openEditModal(<?= $author['id'] ?>, '<?= $author['name'] ?>', '<?= $author['department_office'] ?>', '<?= $author['role'] ?>', '<?= $author['status'] ?>')" class="user_edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="router.php?page=user_management&delete_id=<?= $author['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="user_delete">
                                            <i class="fa-solid fa-trash"></i>
                                            </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <?php else: ?>
                        <p>No data found</p>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="id01" class="modal">
    <form class="modal-content animate" action="" method="post">
        <div class="form-col">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x"></i></button>
        </div>
        <div class="form-container sector-form">
            <div class="form-container-header">
                <h1>User Information</h1>
            </div>
            <input type="hidden" name="status" value="active">

            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for=""><b>Full Name</b></label>
                    <input type="text" name="name" placeholder="Enter full name" required>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for=""><b>Username</b></label>
                    <input type="text" name="username" placeholder="Enter username" required>
                </div>
                <div class="form-col">
                    <label for=""><b>Password</b></label>
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

                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for=""><b>Department</b></label> 
                    <select name="department_office">
                        <option value="" disabled selected>Select Department/Office</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo htmlspecialchars($department['department_office']); ?>">
                                <?php echo htmlspecialchars($department['department_office']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-col">
                    <label for=""><b>Role</b></label>
                    <select name="role" id="" required>
                        <option value=""disabled selected>Select Role</option>
                        <option value="author">Author</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <button class="add_new_user" type="submit">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="updateModal" class="modal">
    <form class="modal-content animate" id="updateUserForm" action="router.php?page=user_management" method="post">
        <div class="form-col">
            <button type="button" onclick="document.getElementById('updateModal').style.display='none'" class="cancelbtn">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="form-container sector-form">
            <div class="form-container-header">
                <h1>Update User Information</h1>
            </div>
            <input type="hidden" name="id" id="update_id">
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for=""><b>Full Name</b></label>
                    <input type="text" name="name" id="update_name" required>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for=""><b>Status</b></label>
                    <select name="status" id="update_status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-col">
                    <label for=""><b>Role</b></label>
                    <select name="role" id="update_role">
                        <option value="admin">Admin</option>
                        <option value="author">Author</option>
                    </select>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
            <div class="form-col">
                    <label for=""><b>Department</b></label>
                    <input type="text" name="department_office" id="update_department_office" required>
                </div>          
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <button class="update_user" type="submit">Update</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="editModal" class="modal">
    <form class="modal-content animate" action="update_user.php" method="post">
        <div class="form-col">
            <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x"></i></button>
        </div>
        <div class="form-container">
            <div class="form-container-header">
                <h1>Edit User</h1>
            </div>
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="form-row">
                <div class="form-col">
                    <label><b>Name</b></label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-col">
                    <label><b>Department</b></label>
                    <input type="text" id="edit_department" name="department_office" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label><b>Role</b></label>
                    <select id="edit_role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="author">Author</option>
                    </select>
                </div>
                <div class="form-col">
                    <label><b>Status</b></label>
                    <select id="edit_status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <button type="submit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // // Open the Edit modal and populate the fields with the sector data
    // function openEditModal(id, sectorName) {
    //     document.getElementById('edit_sector_name').value = sectorName;
    //     document.getElementById('edit_sector_id').value = id;
    //     document.getElementById('editModal').style.display = 'block';
    // }

    function openEditModal(id, name, department_office, role, status) {
    document.getElementById('updateModal').style.display = 'block';
    document.getElementById('update_id').value = id;
    document.getElementById('update_name').value = name;
    document.getElementById('update_department_office').value = department_office;
    document.getElementById('update_role').value = role;
    document.getElementById('update_status').value = status;
}

</script>

<script src="public/js/modal.js"></script>
<script src="public/js/form-validation.js"></script>
</body>
</html>
