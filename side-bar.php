<?php
$role = $_SESSION['role']; 
?>
<nav class="side-navbar">
    <div class="side-bar-header">
        <i class="fa-brands fa-slack"></i>
        <h1>AIP</h1>
    </div>
    <ul class="nav-links">
        <h3 class="side-bar-category">Menu</h3>
        <li class="nav-link">
            <i class="fa-solid fa-chart-simple"></i>
            <a href="router.php?page=home">Dashboard</a>
        </li>
        <li class="nav-link">
            <i class="fa-brands fa-atlassian"></i>
            <a href="router.php?page=classification">Classification</a>
        </li>
        <li class="nav-link has-dropdown">
            <i class="fa-solid fa-diagram-project"></i>
            <a href="#" aria-disabled="true" readonly>AIP</a>
            <span class="dropdown-arrow"></span>
            <ul class="drop-down">
                <?php if ($role === 'author'): ?>
                    <li><i class="fa-solid fa-plus"></i><a href="router.php?page=add-header">Header</a></li>
                    <li><i class="fa-solid fa-plus"></i><a href="router.php?page=add-aip">Project</a></li>
                    <li><i class="fa-solid fa-plus"></i><a href="router.php?page=add-plan">Plan</a></li>
                <?php endif; ?>
                <li><i class="fa-solid fa-table-list"></i><a href="router.php?page=aip">AIP list</a></li>
                <li><i class="fa-solid fa-thumbs-up"></i><a href="router.php?page=aip-approve">AIP Approved</a></li>

                <!-- AIP FUND PROPER (DISABLED IF AUTHOR) -->
                <li>
    <i class="fa-solid fa-filter-circle-dollar"></i>
    <?php if ($role === 'author'): ?>
        <a href="#" onclick="alert('ONLY ADMIN CAN ACCESS THIS OPTION')" style="color: gray; cursor: not-allowed;">AIP Fund Proper</a>
    <?php else: ?>
        <a href="router.php?page=aip-fund-proper">AIP Fund Proper</a>
    <?php endif; ?>
</li>

            </ul>
        </li>

        <?php if ($role !== 'author'): ?>
            <h3 class="side-bar-category">Other</h3> 
            <li class="nav-link">
                <i class="fa-solid fa-users"></i>
                <a href="router.php?page=sector">Sector</a>
            </li>     
            <li class="nav-link">
                <i class="fa-solid fa-network-wired"></i>
                <a href="router.php?page=department">Department</a>
            </li>
        <?php endif; ?>  

        <?php if ($role === 'super-admin'): ?>
            <li class="nav-link">
                <i class="fa-solid fa-user"></i>
                <a href="router.php?page=user_management">User Management</a>
            </li> 
        <?php endif; ?>

        <div class="logout_container">
            <a href="router.php?page=logout"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        </div>
    </ul>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggles = document.querySelectorAll('.nav-link.has-dropdown');

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            // Close any open dropdowns
            dropdownToggles.forEach(item => {
                if (item !== this) {
                    item.classList.remove('active');
                }
            });

            // Toggle the clicked dropdown
            this.classList.toggle('active');
        });
    });
});
</script>
