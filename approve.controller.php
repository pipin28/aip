<?php

// Fetch distinct implementing_office and start_date values
$query_office = "SELECT DISTINCT implementing_office FROM parent";
$result_office = mysqli_query($conn, $query_office);

$query_date = "SELECT DISTINCT DATE_FORMAT(start_date, '%Y-%m') AS start_month FROM parent ORDER BY start_month";
$result_date = mysqli_query($conn, $query_date);

// Get filtered results based on selected filters (if any)
$implementing_office = $_GET['implementing_office'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$sectorCategory = $_GET['sectorCategory'] ?? ''; // Ensure this is set

// Build the WHERE clause based on selected filters
$where_conditions = ["status = 'approved'"]; // Default condition
if ($implementing_office) {
    $where_conditions[] = "implementing_office = '" . mysqli_real_escape_string($conn, $implementing_office) . "'";
}
if ($start_date) {
    $where_conditions[] = "DATE_FORMAT(start_date, '%Y-%m') = '" . mysqli_real_escape_string($conn, $start_date) . "'";
}
if ($sectorCategory) {
    $where_conditions[] = "sector_category = '" . mysqli_real_escape_string($conn, $sectorCategory) . "'";
}
if ($role === 'author') {
    $where_conditions[] = "user_id = '" . mysqli_real_escape_string($conn, $user_id) . "'";
}

// Final query to fetch the filtered data from the parent table
$where_sql = " WHERE " . implode(" AND ", $where_conditions);
$query_filtered = "SELECT * FROM parent" . $where_sql . " ORDER BY id";

$result_filtered = mysqli_query($conn, $query_filtered);

// Error handling for query execution
if (!$result_filtered) {
    die("Query failed: " . mysqli_error($conn));
}


// Function to fetch children (expected outputs) for a given parent
function getChildren($conn, $parent_id) {
    $childrenQuery = "SELECT * FROM child WHERE parent_id = $parent_id ORDER BY id";
    $childrenResult = mysqli_query($conn, $childrenQuery);
    return mysqli_fetch_all($childrenResult, MYSQLI_ASSOC);
}

// Query to get signatories from tbl_header by joining with tbl_user
$signatoriesQuery = "
    SELECT 
        tbl_header.signatory_one,
        tbl_header.signatory_two,
        tbl_header.signatory_three
    FROM 
        tbl_user
    JOIN 
        tbl_header ON tbl_user.id = tbl_header.department_id
    WHERE 
        tbl_user.id = '$user_id'
        ORDER BY tbl_header.id DESC";  // Filter by user_id

$signatoriesResult = mysqli_query($conn, $signatoriesQuery);

// Fetch signatories data if available
if ($signatoriesResult) {
    $signatories = mysqli_fetch_assoc($signatoriesResult);
} else {
    $signatories = null;  // Set to null if no data is found
}