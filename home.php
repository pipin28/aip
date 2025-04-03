<?php
include 'src/config/database.php';

session_start();  // Start the session

// Redirect to login page if the user is not logged in or the status is not 'department'
if (!isset($_SESSION['username'])) {
    header("Location: router.php?page=login");  // Redirect to login page if the user is not logged in or does not have 'department' status
    exit;
}

$user_id = $_SESSION['user_id'];  
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
$query = "SELECT id, aip_ref_code FROM parent"; 

} else {
    $query = "SELECT id, aip_ref_code FROM parent WHERE user_id = '$user_id'"; 
}

$result = mysqli_query($conn, $query);
$parents = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $parents[] = $row;
    }
}

// Function to fetch child data
function getChildren($conn, $parentId) {
    $query = "SELECT personal_services, maintenance_expenses, capital_outlay FROM child WHERE parent_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $parentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $children = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $children[] = $row;
        }
    }
    return $children;
}

// Calculate amounts
$amounts = array_map(function($parent) use ($conn) {
    $children = getChildren($conn, $parent['id']);
    return array_reduce($children, function($carry, $child) {
        return $carry + ($child['personal_services'] ?? 0) + ($child['maintenance_expenses'] ?? 0) + ($child['capital_outlay'] ?? 0);
    }, 0);
}, $parents);


$query = "SELECT id, parent_id, description, personal_services, maintenance_expenses, capital_outlay FROM child";
$result = mysqli_query($conn, $query);

// Variables to store the highest total and corresponding child data
$highestTotal = 0;
$highestChild = null;

// Loop through each row and calculate the total funding
while ($row = mysqli_fetch_assoc($result)) {
    $totalFunding = ($row['personal_services'] ?? 0) + ($row['maintenance_expenses'] ?? 0) + ($row['capital_outlay'] ?? 0);

    // Check if this total is the highest
    if ($totalFunding > $highestTotal) {
        $highestTotal = $totalFunding;
        $highestChild = $row;
    }
}

if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
    $aip_query = "SELECT * FROM parent ORDER by id DESC LIMIT 4";
    
    } else {
        $aip_query = "SELECT * FROM parent WHERE user_id='$user_id' ORDER by id DESC LIMIT 4";
    }
    
$aip_resut = mysqli_query($conn, $aip_query);

if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
    $user_query = "SELECT * FROM tbl_user WHERE role !='super-admin' ORDER by id DESC LIMIT 4";

} else {
    $user_query = "SELECT * FROM tbl_user WHERE role ='author' AND id = '$user_id' ORDER by id DESC LIMIT 4";
}

$user_result = mysqli_query($conn, $user_query);



if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
    $status_query = "
    SELECT 
        status, 
        COUNT(*) AS count 
    FROM parent 
    GROUP BY status";
    
    } else {
        $status_query = "
        SELECT 
            status, 
            COUNT(*) AS count 
        FROM parent WHERE user_id = '$user_id'
        GROUP BY status";
    }

$status_result = mysqli_query($conn, $status_query);

$status_counts = [];
if ($status_result) {
    while ($row = mysqli_fetch_assoc($status_result)) {
        $status_counts[$row['status']] = $row['count'];
    }
}

?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include 'public/components/header.php'; ?>
            <div class="hero">
                <div class="hero-container">
                    <div class="hero-box hero-col-1">
                        <div class="card-wrapper">
                            <div class="hero-card card-1">
                                <i class="fa-solid fa-folder-open"></i>
                                <h1> <?php
                                if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin') {
                                        $dash_member_query = "SELECT COUNT(*) AS total_project FROM parent";
                                        $dash_member_query_run = mysqli_query($conn, $dash_member_query);
                                    
                                        if($dash_member_query_run)
                                        {
                                            // Fetch the result as an associative array
                                            $result = mysqli_fetch_assoc($dash_member_query_run);
                                            // Access the total_room value
                                            echo '<h1>' . $result['total_project'] .' Projects</h1>';
                                        }
                                        else
                                        {
                                            echo '<h1>No data</h1>';
                                        }
                                    } else {
                                        $dash_member_query = "SELECT COUNT(*) AS total_project FROM parent WHERE user_id = '$user_id'";
                                        $dash_member_query_run = mysqli_query($conn, $dash_member_query);
                                    
                                        if($dash_member_query_run)
                                        {
                                            // Fetch the result as an associative array
                                            $result = mysqli_fetch_assoc($dash_member_query_run);
                                            // Access the total_room value
                                            echo '<h1>' . $result['total_project'] .' Projects</h1>';
                                        }
                                        else
                                        {
                                            echo '<h1>No data</h1>';
                                        }
                                    }
                                    ?></h1>
                            </div>
                            <?php  if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super-admin')  :?>
                            <div class="hero-card card-2">
                                <i class="fa-solid fa-user-gear"></i>
                                <h1>
                                    <?php
                                    
                                        $sec_query = "SELECT sector_name, COUNT(*) AS total_count
                                            FROM tbl_sector";
                                        $sec_result = mysqli_query($conn, $sec_query);

                                        if($sec_result)
                                        {
                                            // Fetch the result as an associative array
                                            $result = mysqli_fetch_assoc($sec_result);
                                            // Access the total_room value
                                            echo '<h1>' . $result['total_count'] .' Sectors</h1>';
                                        }
                                        else
                                        {
                                            echo '<h1>No data</h1>';
                                        }
                                    
                                    ?>
                                </h1>
                            </div>
                            <?php endif ?>
                            <div class="hero-card card-3">
                                 <i class="fa-solid fa-arrow-up-wide-short"></i>
                                <h1>₱<?php echo number_format($highestTotal, 2); ?></h1>
                            </div>
                            <div class="hero-card card-4">
                            <i class="fa-solid fa-thumbs-up"></i>
                                <h1>
                                    <?php
                                    
                                        $sec_query = "SELECT status, COUNT(*) AS total_count
                                            FROM parent WHERE status = 'approved'";
                                        $sec_result = mysqli_query($conn, $sec_query);

                                        if($sec_result)
                                        {
                                            // Fetch the result as an associative array
                                            $result = mysqli_fetch_assoc($sec_result);
                                            // Access the total_room value
                                            echo '<h1>' . $result['total_count'] .' Approved</h1>';
                                        }
                                        else
                                        {
                                            echo '<h1>No data</h1>';
                                        }
                                    
                                    ?>
                                </h1>
                            </div>
                        </div>
                     
                            <canvas id="fundingGraph" style="height: 300px; max-height: 300px;"></canvas>
                     
                        <div class="aip-table-list">
                            <div class="aip-list-wrapper">
                                <table>
                                    <thead>
                                        <tr>
                                           
                                            <th>AIP Code</th>
                                            <th>Project</th>
                                            <th>Office</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($aip = mysqli_fetch_assoc($aip_resut)) :?>
                                            <tr>
                                             
                                                <td><?= $aip['aip_ref_code'] ?></td>
                                                <td><?= $aip['description'] ?></td>
                                                <td><?= $aip['implementing_office'] ?></td>
                                                <td >
                                                   <p class="status <?= strtolower(str_replace(' ', '-', $aip['status'])) ?>"> <?= $aip['status'] ?></p>
                                                </td>

                                            </tr>
                                        <?php endwhile ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="hero-box hero-col-2">
                        <div class="get-help-card">
                            <div class="get-help-details">
                                <p>The Annual Investment Program (AIP) is a
                                yearly plan outlining priority projects and their budgets 
                                for local government development.</p>
                            <button type="button">Get help<i class="fa-solid fa-question"></i></button>
                            </div>
                        </div>
                        <div class="pie-graph-container">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                        <div class="user-table">
                            <div class="aip-list-wrapper" style="width:250px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($user = mysqli_fetch_assoc($user_result)) :?>
                                            <tr>
                                             
                                                <td><?= $user['name'] ?></td>
                                                <td><?= $user['role'] ?></td>
                                                <td class="active-status">
                                                <p><?= $user['status'] ?></p>
                                                <?php
                                                if($user['status'] == 'active'){
                                                   echo '<span class="status active"></span>';
                                                }else{
                                                    echo '<span class="status in-active"></span>';
                                                } 
                                                ?>
                                                </td>
                                            </tr>
                                        <?php endwhile ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
  const programs = <?php echo json_encode(array_column($parents, 'aip_ref_code')); ?>;
const amounts = <?php echo json_encode(array_map(function($parent) use ($conn) {
    $children = getChildren($conn, $parent['id']);
    return array_reduce($children, function($carry, $child) {
        return $carry + ($child['personal_services'] ?? 0) + ($child['maintenance_expenses'] ?? 0) + ($child['capital_outlay'] ?? 0);
    }, 0);
}, $parents)); ?>;

// Generate a unique color for each bar
const colors = programs.map(() => `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 1)`);
const borderColors = programs.map(() => `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 0)`);

// Chart.js Configuration
const ctx = document.getElementById('fundingGraph').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: programs,
        datasets: [{
            label: 'Total Funding (in PHP)',
            data: amounts,
            backgroundColor: colors, // Use the array of random colors
            borderColor: borderColors, // Use matching border colors
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
<script>
    const statusData = <?php echo json_encode($status_counts); ?>;

    // Extract labels and values
    const statusLabels = Object.keys(statusData);
    const statusValues = Object.values(statusData);

    // Define colors for each status
    const statusColors = {
        'pending': '#fc6042', // Yellow
        'evaluated': '#2c82c9', // Blue
        're-submitted': '#eee657', // Orange
        'approved': '#2cc990', // Green
        'sent' : '#2fc3e7'
    };

    const chartColors = statusLabels.map(label => statusColors[label] || 'rgba(200, 200, 200, 0.8)');

    // Create the pie chart
    const pieCtx = document.getElementById('statusPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: chartColors,
                borderColor: chartColors.map(color => color.replace('0.8', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
</body>
</html>