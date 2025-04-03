<?php
// File for Approved AIP View
include 'src/config/database.php';
session_start();

// Get user_id and role from session
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
$start_date = $_GET['start_date'] ?? '';
$implementing_office = $_GET['implementing_office'] ?? '';

// Redirect to login page if the user is not logged in
if (!$user_id || !$role) {
    header("Location: router.php?page=login");
    exit;
}

include 'src/controller/approve.controller.php';
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
    .btn-send { background-color: #28a745; color: white; }
    .btn-print { background-color: #007bff; color: white; }
    .btn-excel { background-color: #ffc107; color: black; }

    /* Additional print-specific CSS */
    @media print {
    @page {
        size: legal landscape;
        margin: 10mm; /* Reduce margin to allow more space */
    }

    body {
        visibility: hidden;
        font-family: Arial, sans-serif;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        display: flex;
        justify-content: flex-start; /* Align content to the left */
        align-items: flex-start; /* Align to the top */
        width: 100vw;
    }

    .aip-table, .aip-table * {
        visibility: visible;
        position: relative;
    }

    .aip-table {
        width: 100%; /* Use full width of the paper */
        border-collapse: collapse;
        text-align: left;
        table-layout: fixed;
        transform: translate(-35mm, -50mm); /* Move table 20mm to the left */
    }

    .aip-table th, .aip-table td {
        font-size: 10px;
        padding: 5px;
        border: 1px solid black;
        word-wrap: break-word;
        white-space: normal;
    }

    .aip-table th {
        background-color: #2c71d8 !important;
        color: white !important;
    }

    .aip-table tr:last-child {
        font-weight: bold;
        background-color: #f2f2f2 !important;
    }

    /* Hide unnecessary elements */
    button, .filter-container-approve, .action-btns, .back-btn {
        display: none;
    }
    .footer-date {
        font-weight: bold;
    }

    .footer-signatory {
        font-style: italic;
    }

    /* Print-specific header */
    .print-header {
        display: block;
        text-align: center;
        margin-bottom: 20px;
    }
}

@media screen {
    .print-header {
        display: none;
    }
}

</style>


<link rel="stylesheet" href="public/css/table.css">
<?php include 'public/components/header.php'; ?>

<div class="hero">
    <div class="print-header">
        <h1>Annual Investment Program (AIP)</h1>
        <p>By Program/Project/Activity by Sector</p>
        <p>Date: <?= date("Y-m-d"); ?></p>
    </div>

    <div class="aip_header_container">
        <a class="back-btn" href="router.php?page=aip"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="aip-header approve-header">
            <h1>Annual Investment Program (AIP) <br>By Program/Project/Activity by Sector</h1>
        </div>

        <p style="color:red;">Current Role: <?= $role ?></p>

        <div class="second_header_container">
            <div class="filter-container-approve">
                <form method="GET" action="">
                    <input type="hidden" name="page" value="aip-approve">
                    <label for="start_date">Start Date:</label>
                    <select name="start_date" id="start_date">
                        <option value="">All Dates</option>
                        <?php while ($row_date = mysqli_fetch_assoc($result_date)): ?>
                            <option value="<?= $row_date['start_month']; ?>" <?= ($start_date == $row_date['start_month']) ? 'selected' : ''; ?>>
                                <?= $row_date['start_month']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label for="implementing_office">Implementing Office:</label>
                    <select name="implementing_office" id="implementing_office" class="sector_filter"
                            <?= ($role === 'author') ? 'disabled' : ''; ?>>
                        <option value="<?= htmlspecialchars($_SESSION['department_office']); ?>" selected>
                            <?= htmlspecialchars($_SESSION['department_office']); ?>
                        </option>

                        <?php while ($row_office = mysqli_fetch_assoc($result_office)): ?>
                            <?php if ($row_office['implementing_office'] != $_SESSION['department_office']): ?>
                                <option value="<?= $row_office['implementing_office']; ?>" <?= ($implementing_office == $row_office['implementing_office']) ? 'selected' : ''; ?>>
                                    <?= $row_office['implementing_office']; ?>
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
                </form>
            </div>

            <div class="action-btns">
                <?php if ($role === 'author'): ?>
                    <a href="router.php?page=excel&user_id=<?= $user_id; ?>" class="btn-excel"><i class="fa-solid fa-download"></i> Save to Excel</a>
                <?php else: ?>
                    <a href="router.php?page=excel<?= $start_date ? '&start_date='.urlencode($start_date) : ''; ?><?= $implementing_office ? '&implementing_office='.urlencode($implementing_office) : ''; ?>" class="btn-excel">
                    <i class="fa-solid fa-download"></i> Save to Excel
                    </a>
                <?php endif; ?>
                <!-- Print Button -->
                <button class="btn-print" onclick="printFilteredTable();"><i class="fa-solid fa-print"></i> Print</button>
            </div>
        </div>
    </div>


    <div class="aip-table">
        <table id="aip-table">
            <thead>
                <tr>
                    <th>AIP Ref Code</th>
                    <th>Program/Project/Activity Description</th>
                    <th>Implementing Office</th>
                    <th colspan="2">Schedule of Implementation</th>
                    <th>Expected Outputs</th>
                    <th>Funding Source</th>
                    <th>Personal Services</th>
                    <th>Maintenance and Other Operating Expenses</th>
                    <th>Capital Outlay</th>
                    <th>Total (8+9+10)</th>
                    <th>Climate Change Adaptation</th>
                    <th>Climate Change Mitigation</th>
                    <th>CC Typology Code</th>
                </tr>
                <tr>
                    <th></th><th></th><th></th>
                    <th>Start</th><th>End</th>
                    <th></th><th></th><th></th><th></th><th></th>
                    <th></th><th></th><th></th><th></th>
                </tr>
            </thead>

            <tbody>
                <?php 
                    $total_ps = $total_mooe = $total_co = $total_adapt = $total_mitigate = 0;
                    while ($parent = mysqli_fetch_assoc($result_filtered)):
                        $children = getChildren($conn, $parent['id']);
                        $rowspan = count($children) ?: 1;
                        foreach ($children as $index => $child):
                ?>
                <tr>
                    <?php if ($index === 0): ?>
                        <td rowspan="<?= $rowspan; ?>"><?= htmlspecialchars($parent['aip_ref_code']); ?></td>
                        <td rowspan="<?= $rowspan; ?>"><?= htmlspecialchars($parent['description']); ?></td>
                        <td rowspan="<?= $rowspan; ?>"><?= htmlspecialchars($parent['implementing_office']); ?></td>
                        <td rowspan="<?= $rowspan; ?>"><?= date("M. Y", strtotime($parent['start_date'])); ?></td>
                        <td rowspan="<?= $rowspan; ?>"><?= date("M. Y", strtotime($parent['end_date'])); ?></td>
                    <?php endif; ?>
                    <td><?= $child['description'] ?? '-'; ?></td>
                    <td><?= $child['funding_source'] ?? '-'; ?></td>
                    <td><?= number_format($child['personal_services'] ?? 0, 2); $total_ps += $child['personal_services'] ?? 0; ?></td>
                    <td><?= number_format($child['maintenance_expenses'] ?? 0, 2); $total_mooe += $child['maintenance_expenses'] ?? 0; ?></td>
                    <td><?= number_format($child['capital_outlay'] ?? 0, 2); $total_co += $child['capital_outlay'] ?? 0; ?></td>
                    <td><?= number_format(($child['personal_services'] ?? 0) + ($child['maintenance_expenses'] ?? 0) + ($child['capital_outlay'] ?? 0), 2); ?></td>
                    <td><?= number_format($child['climate_change_adaptation'] ?? 0, 2); $total_adapt += $child['climate_change_adaptation'] ?? 0; ?></td>
                    <td><?= number_format($child['climate_change_mitigation'] ?? 0, 2); $total_mitigate += $child['climate_change_mitigation'] ?? 0; ?></td>
                    <td><?= $child['cc_typology_code'] ?? '-'; ?></td>
                </tr>
                <?php endforeach; endwhile; ?>
                <!-- Totals Row -->
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="7">Grand Total</td>
                    <td><?= number_format($total_ps, 2); ?></td>
                    <td><?= number_format($total_mooe, 2); ?></td>
                    <td><?= number_format($total_co, 2); ?></td>
                    <td><?= number_format($total_ps + $total_mooe + $total_co, 2); ?></td>
                    <td><?= number_format($total_adapt, 2); ?></td>
                    <td><?= number_format($total_mitigate, 2); ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<script>
    function printFilteredTable() {    
        window.print();
    }
</script>