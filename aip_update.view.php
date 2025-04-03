<?php
//File for Aip Update View
include 'src/config/database.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure we have a valid child_id
if (!isset($_GET['child_id'])) {
    echo "Child ID is required.";
    exit();
}

$child_id = $_GET['child_id'];  // Get the child_id from the URL (e.g., aip_update.php?child_id=1)

// Fetch the child record from the database
$query = "SELECT * FROM child WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $child_id);  // Bind child_id to the query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the child record
    $child = $result->fetch_assoc();
} else {
    echo "Child record not found.";
    exit();  // Exit if no record is found
}

// Process the form update if submitted
if (isset($_POST['submit_update'])) {
    // Get the updated data from the form
    $description = $_POST['description'];
    $funding_source = $_POST['funding_source'];
    $personal_services = $_POST['personal_services'];
    $maintenance_expenses = $_POST['maintenance_expenses'];
    $capital_outlay = $_POST['capital_outlay'];
    $climate_adaptation = $_POST['climate_adaptation'];
    $climate_mitigation = $_POST['climate_mitigation'];
    $cc_typology_code = $_POST['cc_typology_code'];

    // Update query
    $update_query = "UPDATE child SET description = ?, funding_source = ?, personal_services = ?, maintenance_expenses = ?, capital_outlay = ?, climate_adaptation = ?, climate_mitigation = ?, cc_typology_code = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssssssi", $description, $funding_source, $personal_services, $maintenance_expenses, $capital_outlay, $climate_adaptation, $climate_mitigation, $cc_typology_code, $child_id);

    if ($update_stmt->execute()) {
        header('Location: router.php?page=aip');
        exit();
    } else {
        echo "Error updating AIP child.";
    }
}

?>
<link rel="stylesheet" href="public/css/table.css">

    <title>Update Child AIP</title>
    <?php include 'public/components/header.php' ?>
<div class="hero">
    <form class="aip-update-form" action="" method="post">
        <div class="form-container">
            <div class="form-container-header">
                <h1>Update Child AIP</h1>
                <p>Please fill out the details below to update the child AIP record.</p>
            </div>

            <!-- Hidden field for the child_id -->
            <input type="hidden" name="child_id" value="<?php echo htmlspecialchars($child['id']); ?>">

            <h4 class="form_sub_header">Child AIP Information</h4>

            <!-- AIP Information Fields -->
            <div class="form-row">
                <div class="form-col">
                    <label>Program/Project/Activity Description:</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($child['description']); ?>" placeholder="Enter program/activity" required><br><br>
                </div>
                <div class="form-col">
                    <label>Funding Source:</label>
                    <input type="text" name="funding_source" value="<?php echo htmlspecialchars($child['funding_source']); ?>" placeholder="Enter funding source" required><br><br>
                </div>
            </div>

            <h4 class="form_sub_header">Financial Information</h4>
            <div class="form-row">
                <div class="form-col">
                    <label>Personal Services:</label>
                    <input type="number" name="personal_services" value="<?php echo htmlspecialchars($child['personal_services']); ?>" placeholder="Enter amount" required><br><br>
                </div>
                <div class="form-col">
                    <label>Maintenance Expenses:</label>
                    <input type="number" name="maintenance_expenses" value="<?php echo htmlspecialchars($child['maintenance_expenses']); ?>" placeholder="Enter amount" required><br><br>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Capital Outlay:</label>
                    <input type="number" name="capital_outlay" value="<?php echo htmlspecialchars($child['capital_outlay']); ?>" placeholder="Enter amount" required><br><br>
                </div>
                <div class="form-col">
                    <label>Climate Adaptation:</label>
                    <input type="number" name="climate_adaptation" value="<?php echo htmlspecialchars($child['climate_adaptation']); ?>" placeholder="Enter amount" required><br><br>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Climate Mitigation:</label>
                    <input type="number" name="climate_mitigation" value="<?php echo htmlspecialchars($child['climate_mitigation']); ?>" placeholder="Enter amount" required><br><br>
                </div>
                <div class="form-col">
                    <label>CC Typology Code:</label>
                    <input type="text" name="cc_typology_code" value="<?php echo htmlspecialchars($child['cc_typology_code']); ?>" placeholder="Enter code" required><br><br>
                </div>
            </div>

            <!-- <h4 class="form_sub_header">Schedule of Implementation</h4>
            <div class="form-row">
                <div class="form-col">
                    <label>Start Date:</label>
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($child['start_date']); ?>" required><br><br>
                </div>
                <div class="form-col">
                    <label>End Date:</label>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($child['end_date']); ?>" required><br><br>
                </div>
            </div> -->

            <div class="form-row">
                <div class="form-col">
                    <button type="submit" name="submit_update">Update AIP</button>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
