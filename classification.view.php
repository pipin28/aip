<?php
include 'src/config/database.php';
//File for Classification View
session_start();  // Start the session

// Redirect to login page if the user is not logged in or the status is not 'department'
if (!isset($_SESSION['username'])) {
    header("Location: router.php?page=sector");  // Redirect to login page if the user is not logged in or does not have 'department' status
    exit;
}

include 'src/controller/classification.controller.php';

function generateAipCode($conn) { 
    // Fixed parts of the AIP code
    $constantPart = 'AIP-';

    // Generate a random 4-digit number for the first part (#000), only the thousands digit changes
    $randomNumber1 = mt_rand(1, 9) . '000'; // Thousands digit (1-9) and fixed "000"

    // Fixed '000' in the second part (no change)
    $fixedNumber = '000';

    // Fixed '2' in the third part (no change)
    $fixedPart = '2';

    // Generate a random 1-digit number for the fourth part (#)
    $randomNumber2 = mt_rand(0, 9); // 1-digit random number (0-9)

    // Generate a 2-digit random number for the last part (##)
    $randomNumber3 = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT); // 2-digit random number (01-99)

    // Combine all parts to form the AIP code
    $newAipCode = $constantPart . $randomNumber1 . '-' . $fixedNumber . '-' . $fixedPart . '-' . $randomNumber2 . '-' . $randomNumber3;

    // Ensure the generated code is unique
    $checkQuery = "SELECT aip_code FROM aip_sector WHERE aip_code = '$newAipCode'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // If duplicate, recursively generate a new code
        return generateAipCode($conn); // This ensures uniqueness by retrying if a duplicate is found
    }

    return $newAipCode;
}




// Fetch data to display in the table
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
    $allDataQuery = "SELECT aip_id, aip_code, department_office, sector_category FROM aip_sector";
} else {
    $user_dept = mysqli_real_escape_string($conn, $_SESSION['department_office']);
    $allDataQuery = "SELECT aip_id, aip_code, department_office, sector_category 
                     FROM aip_sector 
                     WHERE department_office = '$user_dept'";
}
$allDataResult = mysqli_query($conn, $allDataQuery);


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
    <div class="page-2-container">
        <div class="table">
                <div class="table-header">
                    <h1>Annual Investment Program per sector</h1>
                    <h1>(Institutional, Social, Economic, Environmental)</h1>
                </div>
  
    <div class="add-btn class-btn search-wrapper" style="margin-right:18px;">
    <div class="search-bar class-search">
                    <input type="text" id="searchBar" onkeyup="searchDepartments()" placeholder="Search by department office or sector..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <?php if ($role === 'admin'): ?>
        <button onclick="document.getElementById('id01').style.display='block'"><i class="fa-solid fa-plus"></i> Add new</button>
        <?php endif; ?>           
    </div>
    <!-- Consolidated table -->
    <table  id="sectorTable">
        <thead>
            <tr>
                <th>No.</th>
                <th>AIP Code</th>
                <th>Department/Office</th>
                <th>Sector Category</th>
               
            </tr>
        </thead>
        <tbody>
            <?php
            if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
                // Admin: Fetch all data
                $allDataQuery = "SELECT aip_id, aip_code, department_office, sector_category FROM aip_sector";
            } else {
                // Department: Fetch only data matching the user's sector_category
                $user_dept = mysqli_real_escape_string($conn, $_SESSION['department_office']);
                $allDataQuery = "SELECT aip_id, aip_code, department_office, sector_category 
                                FROM aip_sector 
                                WHERE department_office = '$user_dept'";
            }

            $allDataResult = mysqli_query($conn, $allDataQuery);

            if ($allDataResult && mysqli_num_rows($allDataResult) > 0) {
                $rowNumber = 1;
                while ($dataRow = mysqli_fetch_assoc($allDataResult)) { ?>
                    <tr>
                        <td><?= $rowNumber; ?></td>
                        <td><?= htmlspecialchars($dataRow['aip_code']); ?></td>
                        <td><?= htmlspecialchars($dataRow['department_office']); ?></td>
                        <td><?= htmlspecialchars($dataRow['sector_category']); ?></td>
                     
                    </tr>
                    <?php
                    $rowNumber++;
                }
            } else { ?>
                <tr>
                    <td colspan="<?= $_SESSION['role'] === 'author' ? 5 : 4; ?>">No data found</td>
                </tr>
            <?php } ?>
        </tbody>

    </table>
          
        </div>
    </div>
</div>

<!-- Modal for adding new sector data -->
<div id="id01" class="modal">
    <form class="modal-content animate" action="" method="post">
        <div class="form-col">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x"></i></button>
        </div>
        <div class="form-container">
            <div class="form-container-header">
                <h1>Contact Information</h1>
                <p>We'll never share this information with anyone</p>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="aip-code"><b>AIP CODE</b></label>
                    <input type="text" id="aip-code" name="aip_code" value="<?php echo generateAipCode($conn); ?>" readonly required>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="department_office"><b>Department/Office</b></label>
                    <select name="department_office" required>
                        <option value="" disabled selected>Select Department/Office</option>
                        <?php
                        // Loop through each department and create an option tag for each
                        foreach ($departments as $department) {
                            echo '<option value="' . htmlspecialchars($department['department_office']) . '">' . htmlspecialchars($department['department_office']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <label for="sector_category"><b>Category</b></label>
                    <select name="sector_category" required>
                        <option value="" disabled selected>Select Sector</option>
                        <?php while ($row = mysqli_fetch_assoc($sec_result)) : ?>
                            <option value="<?php echo htmlspecialchars($row['sector_name']); ?>">
                                <?php echo htmlspecialchars($row['sector_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="form-row" style="margin-right:20px;">
                <div class="form-col">
                    <button type="submit">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="public/js/search_department.js"></script>
<script src="public/js/modal.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#myTable').DataTable();
  });
</script>
</body>
</html>