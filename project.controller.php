<?php
$user_id = $_SESSION['user_id'];
$sector = $_SESSION['sector_category'];
$office = $_SESSION['department_office'];

// function generateAipRefCode($conn, $department_office) {
//     $prefix = strtoupper(substr($department_office, 0, 3)); // Use first 3 letters of office as prefix
//     $query = "SELECT MAX(aip_ref_code) AS max_code FROM parent WHERE aip_ref_code LIKE '$prefix%'";
//     $result = mysqli_query($conn, $query);
//     $row = mysqli_fetch_assoc($result);
    
//     if ($row['max_code']) {
//         // Extract the numeric part of the highest code and increment it
//         $lastNumber = (int)substr($row['max_code'], strlen($prefix));
//         $newNumber = $lastNumber + 1;
//     } else {
//         $newNumber = 1; // Start from 1 if no existing code
//     }
    
//     // Return the new aip_ref_code
//     return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // E.g., ABC0001
// }

// // Generate aip_ref_code for the current department
// $aip_ref_code = generateAipRefCode($conn, $office);

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
       header("location:router.php?page=aip");
       exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}