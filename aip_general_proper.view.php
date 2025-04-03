<?php
//File for AIP General Proper View
include 'src/config/database.php';
session_start();

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  
$role = $_SESSION['role'];  

// Redirect to login page if the user is not logged in
if (!isset($user_id) || !isset($role)) {
    header("Location: router.php?page=login");
    exit;
}
include 'src/controller/approve.controller.php';

// Fetch all distinct sector categories
$sectorQuery = "SELECT DISTINCT sector_category FROM tbl_user";
$sectorResult = $conn->query($sectorQuery);

// Prepare the query to fetch data for each sector
$dataQuery = "SELECT * FROM tbl_user WHERE sector_category = ?";
$stmt = $conn->prepare($dataQuery);

?>


<style>
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        vertical-align: top;
        font-size: .8rem;
    }
    th {
        background-color: #2c71d8;
        color: white;
        font-size: .9rem;
    }
    button {
        padding: 8px 16px;
        margin: 5px;
        border: none;
        cursor: pointer;
    }
    .btn-send {
        background-color: #28a745;
        color: white;
    }
    .btn-print {
        background-color: #007bff;
        color: white;
    }
    .btn-excel {
        background-color: #ffc107;
        color: black;
    }
        @media print {
            @page {
                size: landscape;
            }

            body, html {
            margin: 0;
            padding: 0;
            height: 100%;
           }
           .aip-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse; /* Make the table borders collapse for better fit */
        }


            body * {
                visibility: hidden;
            }
            .aip-table, .aip-table * {
                visibility: visible;
            }
            .aip-table {
                position: absolute;
                right: 180px;
                bottom: 250px;
            }

            .aip-table::-webkit-scrollbar{
                display: none;
            }

            
        .aip-table thead tr th{
            color: black;
        }
        }

</style>

<link rel="stylesheet" href="public/css/table.css">
<?php include 'public/components/header.php' ?>

<div class="hero">
<?php
    
    // Fetch distinct years from start_date and end_date
    $yearQuery = "SELECT DISTINCT YEAR(start_date) AS year FROM parent ORDER BY year ASC";
    $yearResult = $conn->query($yearQuery);
    
    // Fetch distinct sector categories
    $sectorQuery = "SELECT DISTINCT sector_category FROM tbl_user ORDER BY sector_category ASC";
    $sectorResult = $conn->query($sectorQuery);
    
    // Get filters from the form submission
    $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
    $selectedSector = isset($_GET['sector_category']) ? $_GET['sector_category'] : '';
    
    // Base query for data filtering
    $query = "
        SELECT 
            p.aip_ref_code,
            p.implementing_office AS department_office,
            p.sector_category,
            SUM(c.personal_services) AS total_personal_services,
            SUM(c.maintenance_expenses) AS total_maintenance_expenses,
            SUM(c.capital_outlay) AS total_capital_outlay,
            SUM(c.personal_services + c.maintenance_expenses + c.capital_outlay) AS total_expenses,
            SUM(c.climate_adaptation) AS total_climate_adaptation,
            SUM(c.climate_mitigation) AS total_climate_mitigation
        FROM parent p
        JOIN child c ON p.id = c.parent_id
        WHERE p.status = 'approved'";
    
    // Append filters to the query
    if (!empty($selectedYear)) {
        $query .= " AND YEAR(p.start_date) = '$selectedYear'";
    }
    if (!empty($selectedSector)) {
        $query .= " AND p.sector_category = '$selectedSector'";
    }
    if ($role === 'author') {
        $query .= " AND p.user_id = '$user_id'";
    }
    
    $query .= "
        GROUP BY p.aip_ref_code, p.implementing_office
        ORDER BY p.aip_ref_code ASC";
    
    $result = mysqli_query($conn, $query);
    
    // Initialize grand totals
    $grand_totals = [
        'personal_services' => 0,
        'maintenance_expenses' => 0,
        'capital_outlay' => 0,
        'expenses' => 0,
        'climate_adaptation' => 0,
        'climate_mitigation' => 0
    ];
            ?>
  
    <div class="aip_header_container">
    <a class="back-btn" href="router.php?page=aip"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="aip-header">
            <h1>CEBU CITY ANNUAL INVESTMENT PROGRAM</h1>
        </div>
        <div class="second_header_container">
            <div class="side-header">
                <!-- <p>Province/City/Municipality/Barangay: CEBU CITY</p>
                <div class="check-box">
                    <input type="checkbox">
                    <p>No climate Change Expenditure (Please tick the box if your LGU does not have any climate change expenditure.)</p>
                </div> -->
            </div>
            <?php if ($role === 'author'): ?>
            <!-- <div class="add-btn aip-add-btn">
                <button onclick="window.location.href='router.php?page=add-aip'"><i class="fa-solid fa-plus"></i>Add new</button>
            </div> -->
                <?php endif; ?>
                <div class="action-btns total_fund">
                
                    <?php if ($role === 'author'): ?>
                    <a href="router.php?page=excel&user_id=<?php echo $user_id; ?>" class="btn-excel" role="button"><i class="fa-solid fa-download"></i>Save to Excel</a>
                    <?php else :?>
                <a href="router.php?page=excel_total&year=<?php echo $selectedYear; ?>&sector_category=<?php echo $selectedSector; ?>" class="btn-excel" role="button">
                        <i class="fa-solid fa-download"></i>Save to excel
                    </a>
                    <?php endif; ?> 
                    </div>
        </div>
        <div class="filter-container">
            <form method="GET" action="router.php">
                <input type="hidden" name="page" value="aip-fund-proper">
                <label for="year">Year:</label>
                <select name="year" id="year">
                    <option value="">All Years</option>
                    <?php while ($row = mysqli_fetch_assoc($yearResult)): ?>
                        <option value="<?php echo $row['year']; ?>" <?php echo ($selectedYear == $row['year']) ? 'selected' : ''; ?>>
                            <?php echo $row['year']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <label for="sector_category">Sector Category:</label>
                <select name="sector_category" class="sector_filter" id="sector_category">
                    <option value="">All Categories</option>
                    <?php while ($row = mysqli_fetch_assoc($sectorResult)): ?>
                        <option value="<?php echo $row['sector_category']; ?>" <?php echo ($selectedSector == $row['sector_category']) ? 'selected' : ''; ?>>
                            <?php echo $row['sector_category']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit"><i class="fa-solid fa-filter"></i>Filter</button>
            </form>
        </div>
    </div>
    

    <!-- AIP Table Section -->
    <div class="aip-table">
        
    <table id="aip-table">
    <thead>
        <tr>
            <th>AIP Ref Code</th>
            <th>Department Office</th>
            <th>Personal Services</th>
            <th>Maintenance Expenses</th>
            <th>Capital Outlay</th>
            <th>Total Expenses</th>
            <th>Climate Change Adaptation</th>
            <th>Climate Change Mitigation</th>
        </tr>
        <tr>
            <th></th>
            <th colspan="1">General Fund Proper</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $current_sector_category = ""; 
    $grand_totals = array(
        'personal_services' => 0,
        'maintenance_expenses' => 0,
        'capital_outlay' => 0,
        'expenses' => 0,
        'climate_adaptation' => 0,
        'climate_mitigation' => 0
    );

    $overall_totals = array(
        'personal_services' => 0,
        'maintenance_expenses' => 0,
        'capital_outlay' => 0,
        'expenses' => 0,
        'climate_adaptation' => 0,
        'climate_mitigation' => 0
    );
    
    // Process data and output rows
    while ($row = mysqli_fetch_assoc($result)): 
        // Check if the sector category has changed
        if ($current_sector_category != $row['sector_category']): 
            if ($current_sector_category != "") {
                // Output the grand total row for the previous sector category
                echo "<tr><td colspan='2' style='font-weight: bold;'>Grand Total:</td>";
                echo "<td style='font-weight: bold;'>".number_format($sector_totals['personal_services'], 2)."</td>";
                echo "<td style='font-weight: bold;'>".number_format($sector_totals['maintenance_expenses'], 2)."</td>";
                echo "<td style='font-weight: bold;'>".number_format($sector_totals['capital_outlay'], 2)."</td>";
                echo "<td style='font-weight: bold;'>".number_format($sector_totals['expenses'], 2)."</td>";
                echo "<td style='font-weight: bold;'>".number_format($sector_totals['climate_adaptation'], 2)."</td>";
                echo "<td style='font-weight: bold;'>".number_format($sector_totals['climate_mitigation'], 2)."</td></tr>";
    
                // Add sector totals to overall totals
                foreach ($sector_totals as $key => $value) {
                    $overall_totals[$key] += $value;
                }
            }
    
            // Reset the current sector category and sector totals
            $current_sector_category = $row['sector_category'];
            $sector_totals = array(
                'personal_services' => 0,
                'maintenance_expenses' => 0,
                'capital_outlay' => 0,
                'expenses' => 0,
                'climate_adaptation' => 0,
                'climate_mitigation' => 0
            );
    
            // Start a new table for this sector category
            echo "<tr><td colspan='8' style='font-weight: bold; text-align:left;'>".$row['sector_category']."</td></tr>";
        endif;
    
        // Output the row data for this record
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['aip_ref_code'])."</td>";
        echo "<td>".htmlspecialchars($row['department_office'])."</td>";
        echo "<td>".number_format($row['total_personal_services'], 2); 
        $sector_totals['personal_services'] += $row['total_personal_services']; echo "</td>";
        echo "<td>".number_format($row['total_maintenance_expenses'], 2); 
        $sector_totals['maintenance_expenses'] += $row['total_maintenance_expenses']; echo "</td>";
        echo "<td>".number_format($row['total_capital_outlay'], 2); 
        $sector_totals['capital_outlay'] += $row['total_capital_outlay']; echo "</td>";
        echo "<td>".number_format($row['total_expenses'], 2); 
        $sector_totals['expenses'] += $row['total_expenses']; echo "</td>";
        echo "<td>".number_format($row['total_climate_adaptation'], 2); 
        $sector_totals['climate_adaptation'] += $row['total_climate_adaptation']; echo "</td>";
        echo "<td>".number_format($row['total_climate_mitigation'], 2); 
        $sector_totals['climate_mitigation'] += $row['total_climate_mitigation']; echo "</td>";
        echo "</tr>";
    endwhile;
    
    // Add the grand total for the last sector category
    if ($current_sector_category != "") {
        echo "<tr><td colspan='2' style='font-weight: bold;'>Grand Total:</td>";
        echo "<td style='font-weight: bold;'>".number_format($sector_totals['personal_services'], 2)."</td>";
        echo "<td style='font-weight: bold;'>".number_format($sector_totals['maintenance_expenses'], 2)."</td>";
        echo "<td style='font-weight: bold;'>".number_format($sector_totals['capital_outlay'], 2)."</td>";
        echo "<td style='font-weight: bold;'>".number_format($sector_totals['expenses'], 2)."</td>";
        echo "<td style='font-weight: bold;'>".number_format($sector_totals['climate_adaptation'], 2)."</td>";
        echo "<td style='font-weight: bold;'>".number_format($sector_totals['climate_mitigation'], 2)."</td></tr>";
    
        // Add the last sector's totals to overall totals
        foreach ($sector_totals as $key => $value) {
            $overall_totals[$key] += $value;
        }
    }
    
    // Output the overall totals at the end of the table
  
  
    ?>
    </tbody>

<tfoot>
    <!-- Overall Grand Total -->
    <tr>
        <td colspan="2" style="font-weight: bold;">Total Annual Investment Program:</td>
        <td style="font-weight: bold;"><?php echo number_format($overall_totals['personal_services'], 2); ?></td>
        <td style="font-weight: bold;"><?php echo number_format($overall_totals['maintenance_expenses'], 2); ?></td>
        <td style="font-weight: bold;"><?php echo number_format($overall_totals['capital_outlay'], 2); ?></td>
        <td style="font-weight: bold;"><?php echo number_format($overall_totals['expenses'], 2); ?></td>
        <td style="font-weight: bold;"><?php echo number_format($overall_totals['climate_adaptation'], 2); ?></td>
        <td style="font-weight: bold;"><?php echo number_format($overall_totals['climate_mitigation'], 2); ?></td>
    </tr>
</tfoot>

</table>
</div>


<div id="id01" class="modal-message">
    <form class="modal-content animate" action="" method="post">
        <div class="form-col message-close">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x" style="font-size: 15px;"></i></button>
        </div>
        <h3>Revise Box</h3>
        <div class="form-message-container">
            <div class="form-row">
                 <div class="form-col">
                    <label for="">To:</label>
                    <input type="text" id="message-aip-ref-code" name="aip_ref_code" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label for="">Message:</label>
                    <textarea name="" rows="10" id=""></textarea>
                 </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i>Send</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function exportToExcel() {
    const table = document.getElementById("aip-table");
    let csv = [];
    for (let row of table.rows) {
        let rowData = [];
        for (let cell of row.cells) {
            rowData.push(cell.textContent.trim());
        }
        csv.push(rowData.join(","));
    }
    const csvContent = csv.join("\n");
    const link = document.createElement("a");
    link.href = "data:text/csv;charset=utf-8," + encodeURIComponent(csvContent);
    link.target = "_blank";
    link.download = "aip_data.csv";
    link.click();
}
</script>

<script>
    function openMessageForm(aipRefCode) {
        // Open the message modal
        document.getElementById('id01').style.display = 'block';

        // Populate the aip_ref_code into the form field (assuming you have a hidden input or text area to hold it)
        document.getElementById('message-aip-ref-code').value = aipRefCode;
    }

    // Function to close the popup when clicked outside
    window.onclick = function(event) {
        var modal = document.getElementById('id01');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
</script>