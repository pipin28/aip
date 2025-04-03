<?php

//File for Controller for AIP Details
// Redirect to login page if the user is not logged in
if (!isset($user_id) || !isset($role)) {
    header("Location: router.php?page=login");
    exit;
}

$aip_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$aip_user_id = isset($_GET['user_id']) ? mysqli_real_escape_string($conn, $_GET['user_id']) : '';
// Get the implementing_office and status from the URL parameters
$implementing_office = isset($_GET['implementing_office']) ? mysqli_real_escape_string($conn, $_GET['implementing_office']) : '';

$parentsQuery = "SELECT * FROM parent WHERE 1";

// Add condition to fil  ter based on implementing_office if provided
if (!empty($implementing_office)) {
    $parentsQuery .= " AND implementing_office = '$implementing_office' AND status != 'approved'";


}

// Add condition to filter based on implementing_office if provided
if (!empty($implementing_office)) {
    $parentsQuery .= " AND implementing_office = '$implementing_office'";
}

// Add sector_category filter if provided
if (!empty($sectorCategory)) {
    $parentsQuery .= " AND sector_category = '$sectorCategory'";
}

// Check if the user status is 'department' and filter based on user_id
if ($role === 'author') {
    $parentsQuery .= " AND user_id = '$user_id' ";  // Use AND to add conditions
}

if ($role === 'admin') {
    $parentsQuery .= " AND status NOT IN ('pending', 'approved')";
}

// Append the ORDER BY clause to sort the results
$parentsQuery .= " ORDER BY id";

// Execute the query
$parentsResult = mysqli_query($conn, $parentsQuery);
$parents = mysqli_fetch_all($parentsResult, MYSQLI_ASSOC);

// Function to fetch children (expected outputs) for a given parent 
function getChildren($conn, $parent_id) {
    $childrenQuery = "SELECT * FROM child WHERE parent_id = $parent_id ORDER BY id";
    $childrenResult = mysqli_query($conn, $childrenQuery);
    return mysqli_fetch_all($childrenResult, MYSQLI_ASSOC);
}

// Construct the URL
$url = "router.php?page=aip-details&implementing_office=$implementing_office&role=$role&user_id=$aip_user_id";

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
        tbl_user.id = '$user_id' ORDER BY tbl_header.id DESC";  

$signatoriesResult = mysqli_query($conn, $signatoriesQuery);

// Fetch signatories data if available
if ($signatoriesResult) {
    $signatories = mysqli_fetch_assoc($signatoriesResult);
} else {
    $signatories = null;  // Set to null if no data is found
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form_type'])) {
        $form_type = $_POST['form_type'];

        // Handle AIP status update forms
        if ($form_type === 'aip_status') {
            $aip_id = mysqli_real_escape_string($conn, $_POST['aip_id']);
            
            if (isset($_POST['evaluate_aip'])) {
                $updateQuery = "UPDATE parent SET status = 'evaluated' WHERE id = '$aip_id'";
                $successMessage = 'AIP project has been evaluated successfully.';
                $failureMessage = 'Failed to evaluate the AIP project. Please try again.';
                
            } elseif (isset($_POST['send_aip'])) {
                $updateQuery = "UPDATE parent SET status = 'sent' WHERE id = '$aip_id' AND status = 'pending'";
                $successMessage = 'AIP project has been sent successfully.';
                $failureMessage = 'Failed to send the AIP project. Please try again.';
            } elseif (isset($_POST['re-submit_aip'])) {
                $updateQuery = "UPDATE parent SET status = 're-submitted' WHERE id = '$aip_id' AND status = 'evaluated'";
                $successMessage = 'AIP project has been sent successfully.';
                $failureMessage = 'Failed to send the AIP project. Please try again.';
            } elseif (isset($_POST['approve_aip'])) {
                $updateQuery = "UPDATE parent SET status = 'approved' WHERE id = '$aip_id'";
                $successMessage = 'AIP project has been approved.';
                $failureMessage = 'Failed to send the AIP project. Please try again.';
            } else {
                echo "<script>alert('Invalid action.');</script>";
                exit;
            }

            // Execute the query and handle the result
            $updateResult = mysqli_query($conn, $updateQuery);

            if ($updateResult) {
                echo "<script>alert('$successMessage');</script>";
                header("Location:$url ");
                exit;
            } else {
                echo "<script>alert('$failureMessage');</script>";
            }
        }

        // Handle comment form submission
        elseif ($form_type === 'comment') {
            $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
            $sender_id = mysqli_real_escape_string($conn, $_POST['sender_id']);
            $aip_ref_code = mysqli_real_escape_string($conn, $_POST['aip_ref_code']);
            $comment = mysqli_real_escape_string($conn, $_POST['comment']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);

            $insert_query = "INSERT INTO tbl_comment (user_id, sender_id, aip_ref_code, comment, status) 
                             VALUES ('$user_id', '$sender_id', '$aip_ref_code', '$comment', '$status')";

            if (mysqli_query($conn, $insert_query)) {
                header("Location:$url ");
                exit;
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }

        // Invalid form_type
        else {
            echo "<script>alert('Invalid form type.');</script>";
        }
    } else {
        echo "<script>alert('Form type not specified.');</script>";
    }
}
