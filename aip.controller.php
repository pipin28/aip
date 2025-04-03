<?php

$sectorCategory = isset($_GET['sector_category']) ? mysqli_real_escape_string($conn, $_GET['sector_category']) : '';

$parentsQuery = "SELECT * FROM parent WHERE 1";  // Ensure there is a valid WHERE clause at the start

// Modify the query based on the user's status
if ($role === 'author') {
    // If the user is an author, add a condition for user_id
    $parentsQuery .= " AND user_id = '$user_id'";
} elseif ($role === 'admin') {
    // If the user is an admin, exclude parents with pending or approved status
    $parentsQuery .= " AND status NOT IN ('pending', 'approved')";
}

// Check for sectorCategory and add it to the query if it's provided
if (!empty($sectorCategory)) {
    $parentsQuery .= " AND implementing_office = '$sectorCategory'";
}

// Finally, order the results by implementing office and ID
$parentsQuery .= " ORDER BY implementing_office, id";

// Execute the query
$parentsResult = mysqli_query($conn, $parentsQuery);
$parents = mysqli_fetch_all($parentsResult, MYSQLI_ASSOC);

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
        tbl_user.id = '$user_id'";  // Filter by user_id

$signatoriesResult = mysqli_query($conn, $signatoriesQuery);

// Fetch signatories data if available
if ($signatoriesResult) {
    $signatories = mysqli_fetch_assoc($signatoriesResult);
} else {
    $signatories = null;  // Set to null if no data is found
}