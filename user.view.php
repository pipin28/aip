<?php
include 'src/config/database.php';
session_start();
//File for User View

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in the session
$role = $_SESSION['role'];  // Assuming status is stored in the session

// Redirect to login page if the user is not logged in
if (!isset($user_id) || !isset($role)) {
    header("Location: router.php?page=login");
    exit;
}
if ($_SESSION['role'] === 'admin') {
    $user_query = "SELECT * FROM tbl_user ORDER by id DESC LIMIT 4";

} else {
    $user_query = "SELECT * FROM tbl_user WHERE role !='admin' ORDER by id DESC LIMIT 4";
}

$user_result = mysqli_query($conn, $user_query);

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
        <h1>USER</h1>
    </div>

    <div class="table">
        <table class="table-sector">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department Office</th>
                    <th>Sector</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <?php if (mysqli_num_rows($user_result) > 0): ?>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($user_result)):?>
                    <tr>
                        <td><?= $user['name'] ?></td>
                        <td><?= $user['department_office'] ?></td>
                        <td><?= $user['sector_category'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td class="active-status">
                             <p><?= $user['status'] ?></p>
                                <?php
                                 if($user['status'] == 'active'){
                                     echo '<span class="status active"></span>';
                                 }else{
                                     echo '<span class="status in-active"></span>';
                                 } 
                                 ?>
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



<script>
    // Open the Edit modal and populate the fields with the sector data
    function openEditModal(id, sectorName) {
        document.getElementById('edit_sector_name').value = sectorName;
        document.getElementById('edit_sector_id').value = id;
        document.getElementById('editModal').style.display = 'block';
    }
</script>

<script src="public/js/modal.js"></script>
</body>
</html>
