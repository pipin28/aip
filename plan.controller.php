<?php

$user_id = $_SESSION['user_id'];
$parentsQuery = "SELECT * FROM parent  WHERE user_id = '$user_id' AND status != 'approved' ORDER BY id";
$parentsResult = mysqli_query($conn, $parentsQuery);
$parents = mysqli_fetch_all($parentsResult, MYSQLI_ASSOC);

// Insert child data after parent selection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $parent_id = mysqli_real_escape_string($conn, $_POST['parent_id']);
    $child_description = mysqli_real_escape_string($conn, $_POST['child_description']);
    $funding_source = mysqli_real_escape_string($conn, $_POST['funding_source']);
    $personal_services = mysqli_real_escape_string($conn, $_POST['personal_services']);
    $maintenance_expenses = mysqli_real_escape_string($conn, $_POST['maintenance_expenses']);
    $capital_outlay = mysqli_real_escape_string($conn, $_POST['capital_outlay']);
    $climate_adaptation = mysqli_real_escape_string($conn, $_POST['climate_adaptation']);
    $climate_mitigation = mysqli_real_escape_string($conn, $_POST['climate_mitigation']);
    $cc_typology_code = mysqli_real_escape_string($conn, $_POST['cc_typology_code']);

    $childQuery = "INSERT INTO child (parent_id, description, funding_source, personal_services, maintenance_expenses, capital_outlay, climate_adaptation, climate_mitigation, cc_typology_code)
                   VALUES ('$parent_id', '$child_description', '$funding_source', '$personal_services', '$maintenance_expenses', '$capital_outlay', '$climate_adaptation', '$climate_mitigation', '$cc_typology_code')";

    if (mysqli_query($conn, $childQuery)) {
        header("location:router.php?page=aip");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    } 
}