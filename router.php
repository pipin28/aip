<?php

$page = isset($_GET['page']) ? $_GET['page'] : 'home';


// Map pages to files and their respective titles
$pages = [
    'home' => ['file' => 'index.php', 'title' => 'Dashboard'],
    'classification' => ['file' => 'classification.php', 'title' => 'Classification'],
    'class-view' => ['file' => 'src/views/classification.view.php', 'title' => 'Classification-view'],
    'add-aip' => ['file' => 'add-project.php', 'title' => 'Add Project'],
    'add-plan' => ['file' => 'add-plan.php', 'title' => 'Add Plan'],
    'aip' => ['file' => 'aip.php', 'title' => 'AIP'],
    'user' => ['file' => 'user.php', 'title' => 'USER'],
    'user_management' => ['file' => 'user_management.php', 'title' => 'USER Management'],
    'department' => ['file' => 'department.php', 'title' => 'Department'],
    'aip-update' => ['file' => 'aip-update.php', 'title' => 'AIP Update'],
    'aip-approve' => ['file' => 'aip-approve.php', 'title' => 'CY 2025 ANNUAL INVESTMENT PROGRAM (AIP) BY PROGRAM/PROJECT/ACTIVITY SECTOR'],
    'excel' => ['file' => 'src/includes/export_excel.php', 'title' => 'AIP'],
    'excel_total' => ['file' => 'src/includes/export_exceltotal.php', 'title' => 'Excel Total'],
    'aip-details' => ['file' => 'aip-details.php', 'title' => 'AIP Details'],
    'sector' => ['file' => 'sector.php', 'title' => 'Sector'],
    'aipsent' => ['file' => 'tests/aip_projectrequest.php', 'title' => 'Project Request'],
    'add-header' => ['file' => 'add-header.php', 'title' => 'Add header'],
    'login' => ['file' => 'src/auth/login.php', 'title' => 'Login'],
    'logout' => ['file' => 'src/auth/logout.php', 'title' => 'logout'],
    'register' => ['file' => 'src/auth/register.php', 'title' => 'Register'],
    'test' => ['file' => 'tests/aip_viewtest.php', 'title' => 'Testing'],
    'aip-fund-proper' => ['file' => 'aip_general_proper.php', 'title' => 'CEBU CITY ANNUAL INVESTMENT PROGRAM CY 2025'],



    // Add other pages here
];

 
// Include the requested file or show a 404
if (array_key_exists($page, $pages)) {
    $title = $pages[$page]['title']; // Get the title for the current page
    include $pages[$page]['file'];
} else {
    $title = '404 Not Found';
    echo "404 Not Found";
}


?>
