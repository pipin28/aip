<?php
include 'src/config/database.php';
session_start();
//File for Department View

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in the session
$role = $_SESSION['role'];  // Assuming status is stored in the session

// Redirect to login page if the user is not logged in
if (!isset($user_id) || !isset($role)) {
    header("Location: router.php?page=login");
    exit;
}

// Handle form submission for adding new department
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_department']) && isset($_POST['department_office']) && isset($_POST['department_init'])) {
    $department_office = mysqli_real_escape_string($conn, $_POST['department_office']);
    $department_init = mysqli_real_escape_string($conn, $_POST['department_init']);
    $status = 'active';

    // Insert into department table
    $stmt = $conn->prepare("INSERT INTO department (department_office, department_init, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $department_office, $department_init, $status);

    if ($stmt->execute()) {
        echo "<div id='error' class='success-message success-box'>
                    <img src='public/images/checked_green.png' alt='Success Icon' style='width:50px; height:50px; margin-right:5px;'>
                    <p>New department added successfully!</p>
                  </div>"; 
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle the delete request for a department
if (isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM department WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        header("Location: router.php?page=department"); // Redirect after deletion
        exit;
    } else {
        echo "Error deleting department: " . $stmt->error;
    }
    $stmt->close();
}

// Handle the edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_department'])) {
    // Validate if `edit_id` exists and is numeric to prevent invalid updates
    if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $department_office = mysqli_real_escape_string($conn, $_POST['department_office']);
        $department_init = mysqli_real_escape_string($conn, $_POST['department_init']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Update department table
        $stmt = $conn->prepare("UPDATE department SET department_office = ?, department_init = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssi", $department_office, $department_init, $status, $edit_id);

        if ($stmt->execute()) {
            echo "<div id='error' class='success-message success-box'>
                        <img src='public/images/checked_green.png' alt='Success Icon' style='width:50px; height:50px; margin-right:5px;'>
                        <p>Department updated successfully!</p>
                      </div>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid department ID for update.";
    }
}
// Fetch departments from database
$sector_query = "SELECT * FROM department";
$sector_result = mysqli_query($conn, $sector_query);
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
        <h1>DEPARTMENT</h1>
    </div>

    <div class="table">
            <div class="add-btn search-wrapper">
                <div class="search-bar">
                    <input type="text" id="searchBar" onkeyup="searchDept()" placeholder="Search by department office or initial..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <?php if ($role === 'admin'): ?>
                <button onclick="document.getElementById('id01').style.display='block'"><i class="fa-solid fa-plus"></i> Add new</button>
                <?php endif; ?>   
            </div>
    

        <table class="table-sector"  id="sectorTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department Office</th>
                    <th>Status</th>
                    <?php if ($role === 'admin'): ?>
                    <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <?php if (mysqli_num_rows($sector_result) > 0): ?>
            <tbody>
                <?php while ($sector = mysqli_fetch_assoc($sector_result)): ?>
                    <tr>
                        <td><?= $sector['id'] ?></td>
                        <td><?= $sector['department_office'] ?></td>
                        <td class="active-status">
                             <p><?= $sector['status'] ?></p>
                                <?php
                                 if($sector['status'] == 'active'){
                                     echo '<span class="status active"></span>';
                                 }else{
                                     echo '<span class="status in-active"></span>';
                                 } 
                                 ?>
                        </td>
                        <?php if ($role === 'admin'): ?>
                        <td>
                        <div class="user_action_btn sector_action_btn">
                            <a onclick="editDepartment(<?= $sector['id'] ?>, '<?= $sector['department_office'] ?>', '<?= $sector['department_init'] ?>')"class="user_edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="router.php?page=department&id=<?= $sector['id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this?')"class="user_delete"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <?php else :?>
                <tr><td colspan="4">No data found</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Modal for Adding New Department -->
<div id="id01" class="modal">
    <form class="modal-content animate" action="" method="post">
        <div class="form-col">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x"></i></button>
        </div>
        <div class="form-container">
            <div class="form-container-header">
                <h1>Add Department</h1>
                <p>Enter the department details below</p>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="department_office"><b>Department Office</b></label>
                    <input type="text" name="department_office" placeholder="Enter department name" required>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="department_init"><b>Department Initial</b></label>
                    <input type="text" name="department_init" placeholder="Enter department initial" >
                </div>
            </div>

            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                <button type="submit" name="add_department">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal for Editing Department -->
<div id="editModal" class="modal">
    <form class="modal-content animate" action="" method="post">
  
        <div class="form-col">
            <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x"></i></button>
        </div>
        <div class="form-container">
            <div class="form-container-header">
                <h1>Edit Department</h1>
                <p>Update the department details below</p>
            </div>
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="department_office"><b>Department Office</b></label>
                    <input type="text" name="department_office" id="edit_department_office" placeholder="Enter department name" required>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="department_init"><b>Department Initial</b></label>
                    <input type="text" name="department_init" id="edit_department_init" placeholder="Enter department initial" >
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="status"><b>Status</b></label>
                    <select name="status" id="edit_department_status" required>
                        <option value="active">Active</option>
                        <option value="in-active">In-Active</option>
                    </select>
                </div>
            </div>

            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                <button type="submit" name="edit_department">Update</button>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Modal for Delete Confirmation -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="form-container">
            <h2>Are you sure you want to delete this department?</h2>
            <button onclick="deleteDepartment()">Yes</button>
            <button onclick="document.getElementById('deleteModal').style.display='none'">No</button>
        </div>
    </div>
</div>

<script src="public/js/modal.js"></script>
<script src="public/js/search_dept.js"></script>
<script>
    let deleteId = null;

    function confirmDelete(id) {
        deleteId = id;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function deleteDepartment() {
        if (deleteId !== null) {
            window.location.href = '?delete_id=' + deleteId;
        }
    }
    function editDepartment(id, office, init, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_department_office').value = office;
    document.getElementById('edit_department_init').value = init;
    document.getElementById('edit_department_status').value = status;
    document.getElementById('editModal').style.display = 'block';
}

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
</script>
