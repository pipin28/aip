<?php
session_start();

// Destroy all session data
session_unset();    // Unset all session variables
session_destroy();  // Destroy the session

// Redirect to the login page or home page after logging out
header('Location: router.php?page=login');
exit;
?>
