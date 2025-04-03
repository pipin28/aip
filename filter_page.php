<?php
session_start();

// Example session values (temporary for testing)
$_SESSION['role'] = 'user'; // Change to 'admin', 'super admin', or 'user'
$_SESSION['user_id'] = 2;   // Current logged in user ID

// Simulate selected user from a dropdown or URL param
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

// Dummy office data (replace with real DB query)
$offices = ['Office A', 'Office B', 'Office C'];

// Simulate selected office
$implementing_office = isset($_POST['implementing_office']) ? $_POST['implementing_office'] : '';

// Role & user ID
$role = $_SESSION['role'];
$current_user_id = $_SESSION['user_id'];

// Logic: Disable dropdown if role = 'user' AND current user is NOT the selected user
$disabled = ($role === 'user' && $current_user_id !== $selected_user_id) ? 'disabled' : '';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dropdown Role Test</title>
</head>
<body>

<h2>User ID Selected: <?= $selected_user_id ?? 'None' ?></h2>
<h3>Logged in as: <?= $role ?> (User ID: <?= $current_user_id ?>)</h3>

<form method="post">
    <label for="implementing_office">Implementing Office:</label>
    <select name="implementing_office" id="implementing_office" <?= $disabled ?>>
        <option value="">All Offices</option>
        <?php foreach ($offices as $office): ?>
            <option value="<?= $office ?>" <?= ($implementing_office === $office) ? 'selected' : '' ?>>
                <?= $office ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <input type="submit" value="Submit">
</form>

</body>
</html>
