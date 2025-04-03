<?php


if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin' ) {
    // Admin: Fetch all distinct categories
    $categoriesQuery = "SELECT DISTINCT sector_category FROM aip_sector";
    $categoriesResult = mysqli_query($conn, $categoriesQuery);
} else {
    // Department: Fetch only data matching the user's sector_category
    $userCategory = $_SESSION['sector_category'];
    $categoriesQuery = "SELECT DISTINCT sector_category FROM aip_sector WHERE sector_category = '$userCategory'";
    $categoriesResult = mysqli_query($conn, $categoriesQuery);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aip_code =  $_POST['aip_code'];
    $department = $_POST['department_office'];
    $category = $_POST['sector_category'];
  

    // Insert into aip_sector
    $stmt = $conn->prepare("INSERT INTO aip_sector (aip_code, department_office, sector_category) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $aip_code, $department, $category);

    if ($stmt->execute()) {
        echo "<div id='error' class='success-message success-box'>
                    <img src='public/images/checked_green.png' alt='Success Icon' style='width:50px; height:50px; margin-right:5px;'>
                    <p>New record added successfully!</p>
                    </div>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    // Update the sector_category in tbl_user
    $updateQuery = "UPDATE department SET sector_category = ? WHERE department_office = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $category, $department);
    $updateStmt->execute();
    $updateStmt->close();

    $updateUser = "UPDATE tbl_user SET sector_category = ? WHERE department_office = '$department' ";
    $updateStmt = $conn->prepare($updateUser);
    $updateStmt->bind_param("s", $category);
    $updateStmt->execute();
    $updateStmt->close();
}

$query = "SELECT department_office FROM department  WHERE sector_category IS NULL OR sector_category = ''";
$result = $conn->query($query);

// Check if any results are returned
if ($result->num_rows > 0) {
    // Fetch all rows as an associative array
    $departments = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $departments = []; // No matching records found
}


$sec_query = "SELECT id, sector_name FROM tbl_sector";
$sec_result = mysqli_query($conn, $sec_query);

if (!$sec_result) {
    die("Query Failed: " . mysqli_error($conn));
}