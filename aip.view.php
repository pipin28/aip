<?php
//File for AIP View
include 'src/config/database.php';
session_start();

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in the session
$role = $_SESSION['role'];  // Assuming status is stored in the session

// Redirect to login page if the user is not logged in
if (!isset($user_id) || !isset($role)) {
    header("Location: router.php?page=login");
    exit;
}

include 'src/controller/aip.controller.php';
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 5px;
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
                left: 0;
                top: 0;
            }
        }

</style>

<link rel="stylesheet" href="public/css/table.css">
<?php include 'public/components/header.php' ?>
<div class="hero">
    <div class="aip_header_container">
        <div class="aip-header">
            <h1>Annual Investment Program (AIP) <br>By Program/Project/Activity by Sector</h1>
        </div>
        <div class="second_header_container">
            <div class="side-header">
                <!-- <p>Province/City/Municipality/Barangay: CEBU CITY</p>
                <div class="check-box">
                    <input type="checkbox">
                    <p>No climate Change Expenditure (Please tick the box if your LGU does not have any climate change expenditure.)</p>
                </div> -->
                <div class="search-bar aip-search">
                    <input type="text" id="searchBar" onkeyup="searchAip()" placeholder="Search by department office or initial..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            <?php if ($role === 'author'): ?>
            <!-- <div class="add-btn aip-add-btn">
                <button onclick="window.location.href='router.php?page=add-aip'"><i class="fa-solid fa-plus"></i>Add new</button>
            </div> -->
            <?php endif; ?>
            <div class="action-btns">
                <!-- <button class="btn-print" onclick="printPage()">Print</button>
                <a href="router.php?page=excel" class="btn-excel" role="button">Save to Excel</a> -->
                </div>
        
        </div>
    </div>

    <!-- AIP Table Section -->
    <div class="aip-table">
        <table id="aip-table"  >
            <thead>
                <tr>
                    <th>AIP Ref <br>Code</th>
                    <th>Implementing Office/Department</th>
                    <th>Sector</th>
                    <th>Action</th>
                    
                </tr>
            </thead>
            <tbody>
            <?php
            // Check if there are any entries
            if (empty($parents)) {
                echo '<tr><td colspan="5">No data found</td></tr>';
            } else {
                $displayedOffices = [];

                foreach ($parents as $parent) {
                    $implementingOffice = $parent['implementing_office'];

                    // Skip if the office has already been displayed
                    if (in_array($implementingOffice, $displayedOffices)) {
                        continue;
                    }

                    // Mark this office as displayed
                    $displayedOffices[] = $implementingOffice;

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($parent['aip_ref_code']) . '</td>';
                    echo '<td>' . htmlspecialchars($parent['implementing_office']) . '</td>';
                    echo '<td>' . htmlspecialchars($parent['sector_category']) . '</td>';

                    if (!($role === 'admin' && ($parent['status'] === 'pending' || $parent['status'] === 'approved'))) {
                        echo '<td><a href="router.php?page=aip-details&implementing_office=' . urlencode($parent['implementing_office']) . '&status=' . urlencode($parent['status']) . '&user_id=' . urlencode($parent['user_id']) . '">View</a></td>';
                    }

                    echo '</tr>';
                }
            }
            ?>
        </tbody>

        </table>

        <!-- Signatory Section -->
       
    </div>
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
                    <input type="text" >
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

<script src="public/js/search_department.js"></script>
<script>
function printPage() {
    window.print();
}

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
