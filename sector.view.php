<?php
include 'src/config/database.php';
session_start();
//File for Sector View

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in the session
$role = $_SESSION['role'];  // Assuming status is stored in the session

// Redirect to login page if the user is not logged in
if (!isset($user_id) || !isset($role)) {
    header("Location: router.php?page=login");
    exit;
}

// Handle form submission for adding sector
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_add_sector'])) {
    $sector_name = $_POST['sector_name'];

    // Insert into aip_sector
    $stmt = $conn->prepare("INSERT INTO tbl_sector (sector_name) VALUES (?)");
    $stmt->bind_param("s", $sector_name);

    if ($stmt->execute()) {
        echo "<div id='error' class='success-message success-box'>
                <img src='public/images/checked_green.png' alt='Success Icon' style='width:50px; height:50px; margin-right:5px;'>
                <p>New sector added successfully!</p>
              </div>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle sector deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM tbl_sector WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        header("Location:router.php?page=sector&message=Sector deleted successfully");
        exit();
    } else {
        echo "Error deleting sector.";
    }
}

// Handle sector update (fetching the data for the sector)
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sector_query = "SELECT * FROM tbl_sector WHERE id = ?";
    $sector_stmt = $conn->prepare($sector_query);
    $sector_stmt->bind_param("i", $edit_id);
    $sector_stmt->execute();
    $sector_result = $sector_stmt->get_result();
    $sector_data = $sector_result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_edit_sector'])) {
    $sector_id = $_POST['sector_id'];
    $sector_name = $_POST['sector_name'];

    $stmt = $conn->prepare("UPDATE tbl_sector SET sector_name = ? WHERE id = ?");
    $stmt->bind_param("si", $sector_name, $sector_id);

    if ($stmt->execute()) {
        header("Location: router.php?page=sector&message=Sector updated successfully");
        exit;
    } else {
        echo "Error updating sector: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all sectors for display
$sector_query = "SELECT * FROM tbl_sector";
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
        <h1>SECTOR</h1>
    </div>

    <div class="table">
    
            <div class="add-btn search-wrapper">
                <div class="search-bar">
                    <input type="text" id="searchBar" onkeyup="searchDepartments()" placeholder="Search by sector name..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <?php if ($role === 'admin'): ?>
                <button onclick="document.getElementById('id01').style.display='block'"><i class="fa-solid fa-plus"></i> Add new</button>
                <?php endif; ?>  
            </div>
       
        <table class="table-sector" id="sectorTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sector</th>
                    <?php if ($role === 'admin'): ?>
                    <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <?php if (mysqli_num_rows($sector_result) > 0): ?>
            <tbody>
                <?php while($sector = mysqli_fetch_assoc($sector_result)):?>
                    <tr>
                        <td><?= $sector['id'] ?></td>
                        <td><?= $sector['sector_name'] ?></td>
                        <?php if ($role === 'admin'): ?>
                        <td>
                                <div class="user_action_btn sector_action_btn">
                                    <a onclick="openEditModal(<?= $sector['id'] ?>, '<?= $sector['sector_name'] ?>')"class="user_edit"><i class="fa-solid fa-pen-to-square"></i></a> 
                                    <a href="router.php?page=sector&delete_id=<?= $sector['id'] ?>" onclick="return confirm('Are you sure you want to delete this sector?')" class="user_delete"><i class="fa-solid fa-trash"></i></a>
                                </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <?php else: ?>
                <p>No data found</p>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Modal for adding a new sector -->
<div id="id01" class="modal">
    <form class="modal-content animate" action="" method="post">
        <div class="form-col">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x"></i></button>
        </div>
        <div class="form-container sector-form">
            <div class="form-container-header">
                <h1>Sector Information</h1>
                <p>Enter the details of the new sector</p>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for=""><b>Sector Name</b></label>
                    <input type="text" name="sector_name" placeholder="Enter sector name" required>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <button type="submit" name="submit_add_sector">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Edit Modal (initially hidden) -->
<div id="editModal" class="modal">
    <form class="modal-content animate" action="router.php?page=sector" method="post">
        <div class="form-col">
            <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x"></i></button>
        </div>
        <div class="form-container sector-form">
            <div class="form-container-header">
                <h1>Edit Sector</h1>
            </div>
            <input type="hidden" id="edit_sector_id" name="sector_id">
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for=""><b>Sector Name</b></label>
                    <input type="text" id="edit_sector_name" name="sector_name" placeholder="Edit sector name" required>
                   
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <button type="submit" name="submit_edit_sector">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
    // Open the Edit modal and populate the fields with the sector data
    function openEditModal(id, sectorName) {
    console.log(`Editing Sector - ID: ${id}, Name: ${sectorName}`);
    document.getElementById('edit_sector_name').value = sectorName;
    document.getElementById('edit_sector_id').value = id;
    document.getElementById('editModal').style.display = 'block';
}
</script>

<script src="public/js/modal.js"></script>
<script src="public/js/search_sector.js"></script>
</body>
</html>
