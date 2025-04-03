<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // No need to retrieve department_id from the form, as we use the session value for it
    $signatory_one = mysqli_real_escape_string($conn, $_POST['signatory_one']);
    $signatory_two = mysqli_real_escape_string($conn, $_POST['signatory_two']);
    $signatory_three = mysqli_real_escape_string($conn, $_POST['signatory_three']);

    // Insert the data into the tbl_header table, using $user_id for the department_id
    $parentQuery = "INSERT INTO tbl_header (department_id, signatory_one, signatory_two, signatory_three)
                    VALUES ('$user_id', '$signatory_one', '$signatory_two', '$signatory_three')";

    if (mysqli_query($conn, $parentQuery)) {
        header("Location: router.php?page=add-aip");  // Redirect after successful insertion
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);  // Display error if the query fails
    }
}