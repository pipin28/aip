<?php
//File for Aip-Details View
include 'src/config/database.php';
session_start();

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  
$role = $_SESSION['role'];  

include 'src/controller/aip-details.controller.php';

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
        font-size: .7rem;
        border-radius: 30px;
        width: 100px;
        cursor: pointer;
        transition: .3s;
        margin-bottom: 10px;
    }

    .btn-send:hover{
        background-color: black;
    }

    .approved{
        background-color: #2cc990;
    }
    .re-submit{
        background-color: #eee657;
    }
    .evaluate{
        background-color: #2c82c9;
    }

    .evaluate i,
    .re-submit i,
    .approved i{
        margin-right: 5px;
    }

    .sent{
        background-color: #fcb941;
        
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
    <a class="back-btn" href="router.php?page=aip"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="aip-header">
            <h1>Annual Investment Program (AIP) <br>By Program/Project/Activity by Sector</h1>
        </div>
        <div class="second_header_container">
            <div class="side-header">
                <p>Province/City/Municipality/Barangay: CEBU CITY</p>
                <div class="check-box">
                    <input type="checkbox">
                    <p>No climate Change Expenditure (Please tick the box if your LGU does not have any climate change expenditure.)</p>
                </div>
            </div>
            <?php if ($role === 'author'): ?>
            <!-- <div class="add-btn aip-add-btn">
                <button onclick="window.location.href='router.php?page=add-aip'"><i class="fa-solid fa-plus"></i>Add new</button>
            </div> -->
            <?php endif; ?>
            <div class="action-btns">
              
            <?php if ($aip_status === 'approved'): ?>
                    <button class="btn-print" onclick="printPage()"><i class="fa-solid fa-print"></i>Print</button>
                    <a href="router.php?page=excel&parent_id=<?php echo $parent_id; ?>&status=<?php echo $aip_status; ?>" class="btn-excel" role="button"><i class="fa-solid fa-download"></i>Save to Excel</a>
                </div>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                <div class="message-btn">
                    <a onclick="document.getElementById('id01').style.display='block'">  <i class="fa-solid fa-comment"></i>Comment</a>
                    </a>
                </div>
                <?php endif; ?>
        </div>
        
    </div>

    <!-- AIP Table Section -->
    <div class="aip-table">
        <table id="aip-table">
            <thead>
                <tr>
                    <th>AIP Ref <br>Code</th>
                    <th>Program/Project/Activity <br>Description</th>
                    <th>Implementing <br> Office/<br>Department</th>
                    <th colspan="2">Schedule of <br> Implementation</th>
                    <th>Expected Outputs</th>
                    <th>Funding <br> Source</th>
                    <th colspan="3">Amount</th>
                    <th>TOTAL <br>(8+9+10)</th>
                    <th colspan="2">Amount of Climate <br>Change Expenditures</th>
                    <th>CC <br> Typology <br> Code</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Staring Date</th>
                    <th>Completion Date</th>
                    <th></th>
                    <th></th>
                    <th>Personal Services</th>
                    <th>Maintenance and Other Operating Expenses</th>
                    <th>Capital Outlay</th>
                    <th></th>
                    <th>Climage Change Adaptation</th>
                    <th>Climate Change Mitigation</th>
                    <th></th>
                    <th></th>
                    <th></th>
               
                </tr>
            </thead>
            <tbody>
    <?php 
        $total_personal_services = 0;
        $total_maintenance_expenses = 0;
        $total_capital_outlay = 0;
        $total_climate_adaptation = 0;
        $total_climate_mitigation = 0;
        $total_cc_typology_code = 0;

        foreach ($parents as $parent): 
            $children = getChildren($conn, $parent['id']);
            $childCount = count($children);
            $rowspan = $childCount > 0 ? $childCount : 1;
    ?>
        <?php foreach ($children as $index => $child): ?>
            <tr>
                <?php if ($index === 0): ?>
                    <td rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($parent['aip_ref_code']); ?></td>
                    <td rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($parent['description']); ?></td>
                    <td rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($parent['implementing_office']); ?></td>
                    <td rowspan="<?php echo $rowspan; ?>"><?php echo date("M. Y", strtotime($parent['start_date'])); ?></td>
                    <td rowspan="<?php echo $rowspan; ?>"><?php echo date("M. Y", strtotime($parent['end_date'])); ?></td>
                <?php endif; ?>
                <td><?php echo !empty($child['description']) ? htmlspecialchars($child['description']) : '-'; ?></td>
                <td><?php echo !empty($child['funding_source']) ? htmlspecialchars($child['funding_source']) : '-'; ?></td>
                <td><?php echo number_format($child['personal_services'] ?? 0, 2); $total_personal_services += ($child['personal_services'] ?? 0); ?></td>
                <td><?php echo number_format($child['maintenance_expenses'] ?? 0, 2); $total_maintenance_expenses += ($child['maintenance_expenses'] ?? 0); ?></td>
                <td><?php echo number_format($child['capital_outlay'] ?? 0, 2); $total_capital_outlay += ($child['capital_outlay'] ?? 0); ?></td>
                <td><?php echo number_format(($child['personal_services'] ?? 0) + ($child['maintenance_expenses'] ?? 0) + ($child['capital_outlay'] ?? 0), 2); ?></td>
                <td><?php echo number_format($child['climate_adaptation'] ?? 0, 2); $total_climate_adaptation += ($child['climate_adaptation'] ?? 0); ?></td>
                <td><?php echo number_format($child['climate_mitigation'] ?? 0, 2); $total_climate_mitigation += ($child['climate_mitigation'] ?? 0); ?></td>
                <td><?php echo !empty($child['cc_typology_code']) ? htmlspecialchars($child['cc_typology_code']) : '0.00'; ?></td>
                <td><p <?= strtolower(str_replace(' ', '-', $parent['status'])) ?>> <?= $parent['status'] ?></p></td>

                <?php if ($index === 0): ?><!-- Check if it's the last child -->
                    <?php if ($role === 'admin' && in_array($parent['status'], ['re-submitted', 'sent'])): ?>
                        <td>
                            <form method="post">
                                <input type="hidden" name="form_type" value="aip_status">
                                <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                                <button type="submit" name="evaluate_aip" class="btn-send evaluate"><i class="fa-solid fa-rotate-right"></i>Evaluate</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="form_type" value="aip_status">
                                <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                                <button type="submit" name="approve_aip" class="btn-send approved"><i class="fa-solid fa-check"></i>Approve</button>
                            </form>
                        </td>
                    <?php elseif ($role === 'author' && $parent['status'] === 'pending'): ?>
                        <td>
                            <form method="post">
                                <input type="hidden" name="form_type" value="aip_status">
                                <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                                <button type="submit" name="send_aip" class="btn-send">Send</button>
                            </form>
                        </td>
                    <?php elseif ($role === 'author' && $parent['status'] === 'evaluated'): ?>
                        <td>
                            <form method="post">
                                <input type="hidden" name="form_type" value="aip_status">
                                <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                                <button type="submit" name="re-submit_aip" class="btn-send re-submit"><i class="fa-solid fa-rotate-right"></i>Re-submit</button>
                                <a href="router.php?page=aip-update&aip_id=<?php echo htmlspecialchars($parent['id']); ?>&child_id=<?php echo htmlspecialchars($child['id']); ?>&user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>" class="btn-send upload">
                                <i class="fa-solid fa-pen"></i> Update/Redo
                                </a>
                            </form>
                        </td>
                    <?php elseif ($role === 'admin' && $parent['status'] === 're-submitted'): ?>
                        <td>
                            <form method="post">
                                <input type="hidden" name="form_type" value="aip_status">
                                <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                                <button type="submit" name="end_aip" class="btn-send evaluate">Evaluate</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="form_type" value="aip_status">
                                <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                                <button type="submit" name="approve_aip" class="btn-send approved"><i class="fa-solid fa-check"></i>Approve</button>
                            </form>
                        </td>
                    <?php endif; ?>

                    <?php elseif ($role === 'author' && $parent['status'] === 'evaluated'): ?>
                        <td>
                            <form method="post">
                                <input type="hidden" name="form_type" value="aip_status">
                                <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                                <button type="submit" name="re-submit_aip" class="btn-send re-submit">Re-submit</button>
                                <a href="router.php?page=aip-update&aip_id=<?php echo htmlspecialchars($parent['id']); ?>&child_id=<?php echo htmlspecialchars($child['id']); ?>&user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>" class="btn-send upload">
                                    Update/Redo
                                </a>
                            </form>
                        </td>
                <?php endif; ?> <!-- End of last child row check -->
            </tr>
        <?php endforeach; ?>

        <?php if ($childCount == 0): ?>
            <tr>
                <td><?php echo htmlspecialchars($parent['aip_ref_code']); ?></td>
                <td><?php echo htmlspecialchars($parent['description']); ?></td>
                <td><?php echo htmlspecialchars($parent['implementing_office']); ?></td>
                <td><?php echo date("M. Y", strtotime($parent['start_date'])); ?></td>
                <td><?php echo date("M. Y", strtotime($parent['end_date'])); ?></td>
                <td>-</td>
                <td>-</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td><p <?= strtolower(str_replace(' ', '-', $parent['status'])) ?>> <?= $parent['status'] ?></p></td>
                
                <?php if ($role === 'admin' && in_array($parent['status'], ['re-submitted', 'sent'])): ?>
                    <td>
                        <form method="post">
                            <input type="hidden" name="form_type" value="aip_status">
                            <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                            <button type="submit" name="evaluate_aip" class="btn-send evaluate">Evaluate</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="form_type" value="aip_status">
                            <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                            <button type="submit" name="approve_aip" class="btn-send approved"><i class="fa-solid fa-check"></i>Approve</button>
                        </form>
                    </td>
                <?php elseif ($role === 'author' && $parent['status'] === 'pending'): ?>
                    <td>
                        <form method="post">
                            <input type="hidden" name="form_type" value="aip_status">
                            <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                            <button type="submit" name="send_aip" class="btn-send">Send</button>
                        </form>
                    </td>
                <?php elseif ($role === 'author' && $parent['status'] === 'evaluated'): ?>
                    <td>
                        <form method="post">
                            <input type="hidden" name="form_type" value="aip_status">
                            <input type="hidden" name="aip_id" value="<?php echo htmlspecialchars($parent['id']); ?>">
                            <button type="submit" name="re-submit_aip" class="btn-send re-submit">Re-submit</button>
                            <a href="router.php?page=aip-update&aip_id=<?php echo htmlspecialchars($parent['id']); ?>" class="btn-send upload">
                            <i class="fa-solid fa-pen"></i> Update
                            </a>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</tbody>

        </table>


        <!-- Signatory Section -->
        <div class="header-department">
            <?php if ($signatories): ?>
            <div class="head-details">
                <p><?php echo htmlspecialchars($signatories['signatory_one']); ?></p>
                <p>OIC-MICS</p>
                <p>Date:__________</p>
            </div>
            <div class="head-details">
                <p><?php echo htmlspecialchars($signatories['signatory_two']); ?></p>
                <p>OIC-City Officer</p>
                <p>Date:__________</p>
            </div>
            <div class="head-details">
                <p><?php echo htmlspecialchars($signatories['signatory_three']); ?></p>
                <p>Local Chief Executive</p>
                <p>Date:__________</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div id="id01" class="modal-message">
    <form class="modal-content animate" action="" method="post">
        <div class="form-col message-close">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn"><i class="fa-solid fa-x" style="font-size: 15px;"></i></button>
        </div>
        <input type="hidden" name="form_type" value="comment">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($aip_user_id) ?>">
        <input type="hidden" name="sender_id" value="<?= htmlspecialchars($user_id) ?>">
        <input type="hidden" name="status" value="unread">
        <div class="form-message-container">
            <div class="form-row">
                <?php
                    $aip_select = "SELECT id, aip_ref_code, description FROM parent WHERE user_id = '$aip_user_id' AND status !='approved' LIMIT 1";
                    $aip_select_result = mysqli_query($conn,$aip_select);
                ?>
            <div class="form-col">
                    <label for=""><b>AIP</b></label>
                    <select name="aip_ref_code">
                    <option value="" disabled selected>Select AIP</option>
                        <?php while ($row = mysqli_fetch_assoc($aip_select_result)) : ?>
                            <option value="<?php echo htmlspecialchars($row['aip_ref_code']); ?>">
                                <?php echo htmlspecialchars($row['aip_ref_code']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label for="">Message:</label>
                    <textarea name="comment" rows="10" id=""></textarea>
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

<script>

        // Open the message modal


    // Function to close the popup when clicked outside
    window.onclick = function(event) {
        var modal = document.getElementById('id01');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
</script>

