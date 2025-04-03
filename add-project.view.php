<?php
//File for Add-Project View
include 'src/config/database.php';
session_start();  // Start the session

// Redirect to login page if the user is not logged in or the role is not 'author'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'author') {
    header("Location: router.php?page=login");  // Redirect to login page if the user is not logged in or does not have 'author' role
    exit;
}

$user_id = $_SESSION['user_id'];
$sector = $_SESSION['sector_category'];
$office = $_SESSION['department_office'];


// Ensure that the 'department_office' is available from the session
$department_office = isset($_SESSION['department_office']) ? $_SESSION['department_office'] : ''; 

// Fetch the AIP code from the aip_sector table based on the department_office
$sql = "SELECT aip_code FROM aip_sector WHERE department_office = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $department_office); // Use 's' for string binding
$stmt->execute();
$result = $stmt->get_result();

// Initialize the AIP code as empty
$aip_code = '';

// If a match is found, retrieve the AIP code
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $aip_code = $row['aip_code'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $aip_ref_code = mysqli_real_escape_string($conn, $_POST['aip_ref_code']);
    $office = mysqli_real_escape_string($conn, $_POST['description']);
    $implementing_office = mysqli_real_escape_string($conn, $_POST['implementing_office']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $parentQuery = "INSERT INTO parent (user_id, aip_ref_code, sector_category, description, implementing_office, start_date, end_date, status)
                    VALUES ('$user_id','$aip_ref_code',  '$sector', '$office', '$implementing_office', '$start_date', '$end_date', '$status')";

    if (mysqli_query($conn, $parentQuery)) {
        header("Location: router.php?page=aip");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<?php include 'public/components/header.php' ?>

<div class="hero">
    <form action="" method="post">
        <div class="form-container">
            <div class="form-container-header">
                <h1>Annual Investment Program (AIP) Submission</h1>
                <p>Please fill out the details below for the Annual Program Plan. This information will be securely managed and not shared externally.</p>
            </div>

            <input type="hidden" name="status" value="pending">
            <input type="hidden" name="department_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="sector_category" value="<?php echo $user_id; ?>">

            <div class="form-row">
                <div class="form-col">
                    <label for="aip-code"><b>AIP REF CODE</b></label>

                    <input type="text" placeholder="AIP CODE" name="aip_ref_code" value="<?php echo htmlspecialchars($aip_code); ?>" readonly required>
                </div>
            </div>

            <h4 class="form_sub_header">AIP Information</h4>
            <div class="form-row">
                <div class="form-col">
                    <label>Program/Project/Activity Description:</label>
                    <input type="text" name="description" placeholder="Enter program/activity" required><br><br>
                </div>
                <div class="form-col">
                    <label>Implementing Office/Department:</label>
                    <input type="text" name="implementing_office" value="<?php echo $_SESSION['department_office']; ?>" placeholder="Enter implementing/office"><br><br>
                </div>
            </div>

            <h4 class="form_sub_header">Schedule of Implementation</h4>
            <div class="form-row">
                <div class="form-col">
                    <label>Start Date:</label>
                    <input type="date" name="start_date"><br><br>
                </div>
                <div class="form-col">
                    <label>End Date:</label>
                    <input type="date" name="end_date"><br><br>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <button type="submit">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
</body>
</html>
