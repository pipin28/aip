<?php
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include 'src/config/database.php';
session_start();

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$start_date = $_GET['start_date'] ?? '';
$implementing_office = $_GET['implementing_office'] ?? '';

$where_conditions = [];
if ($start_date) {
    $where_conditions[] = "DATE_FORMAT(start_date, '%Y-%m') = '" . mysqli_real_escape_string($conn, $start_date) . "'";
}
if ($implementing_office) {
    $where_conditions[] = "implementing_office = '" . mysqli_real_escape_string($conn, $implementing_office) . "'";
}
if ($role === 'author') {
    $where_conditions[] = "user_id = '" . intval($user_id) . "'";
}

$where_sql = !empty($where_conditions) ? " WHERE " . implode(" AND ", $where_conditions) : "";
$where_sql .= !empty($where_sql) ? " AND status = 'approved'" : " WHERE status = 'approved'";


$query = "
SELECT parent.*, 
       aggregated_data.personal_services, 
       aggregated_data.maintenance_expenses, 
       aggregated_data.capital_outlay, 
       tbl_header.signatory_one, 
       tbl_header.signatory_two, 
       tbl_header.signatory_three 
FROM parent
LEFT JOIN (
    SELECT parent_id, 
           SUM(personal_services) AS personal_services, 
           SUM(maintenance_expenses) AS maintenance_expenses, 
           SUM(capital_outlay) AS capital_outlay
    FROM child
    GROUP BY parent_id
) AS aggregated_data ON parent.id = aggregated_data.parent_id
LEFT JOIN tbl_header ON parent.user_id = tbl_header.department_id 
$where_sql 
ORDER BY parent.id";

$result = mysqli_query($conn, $query);


if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);


$signatory_one = $data[0]['signatory_one'] ?? '-';
$signatory_two = $data[0]['signatory_two'] ?? '-';
$signatory_three = $data[0]['signatory_three'] ?? '-';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
    'G1' => 'CY 2025 Annual Investment Program (AIP)',
    'G2' => 'By Program/Project/Activity by Sector',
    'A4' => 'Province/City/Municipality/Barangay: CEBU CITY',
    'A6' => 'AIP Ref Code',
    'B6' => 'Program/Project/Activity/ Description',
    'B7' => 'Activity Description',
    'C6' => 'Implementing Office',
    'C7' => 'Department',
    'D6' => 'Schedule of Implementation',
    'D8' => 'Starting Date',
    'E7' => 'Implementation',
    'E8' => 'Completion Date',
    'F6' => 'Expected Outputs',
    'G6' => 'Funding Source',
    'H7' => 'Personal Services',
    'H6' => 'Amount',
    'I7' => 'Maintenance and Other Operating Expenses',
    'J7' => 'Capital Outlay',
    'K6' => 'Total (8+9+10)',
    'L6' => 'Amount of Climate Change Expenditures',
    'L7' => 'Climate Change Adaptation',
    'M7' => ' Climate Change Mitigation',
    'N6' => 'CC Typology ',

];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}
$columnWidths = [
    'A' => 12, 'B' => 16, 'C' => 15, 'D' => 10, 'E' => 12, // adjust the table cell size
    'F' => 10, 'G' => 10, 'H' => 10, 'I' => 18, 'J' => 10, 
    'K' => 10, 'L' => 12, 'M' => 12, 'N' => 10
];



foreach ($columnWidths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}
$sheet->getStyle('A6:N8')->applyFromArray([
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFFF00'] // Yellow background, change to your preferred color
    ],
    'font' => [
        'bold' => true
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);
$sheet->mergeCells('A6:A8'); // Merge AIP Ref Code
$sheet->mergeCells('B6:B8'); // Merge Program/Project
$sheet->mergeCells('C6:C8'); // Merge Implementing Office
$sheet->mergeCells('D6:E6'); // Merge Start Date
$sheet->mergeCells('D6:D7'); // Merge Start Date
$sheet->mergeCells('D6:E7'); // Merge Start Date
$sheet->mergeCells('F6:F8'); // Merge Expected Outputs
$sheet->mergeCells('G6:G8'); // Merge Funding Source
$sheet->mergeCells('H7:H8'); // Merge Personal Services
$sheet->mergeCells('I7:I8'); // Merge Maintenance and Other Expenses
$sheet->mergeCells('H6:J6'); // Merge Maintenance and Other Expenses
$sheet->mergeCells('J7:J8'); // Merge Capital Outlay
$sheet->mergeCells('K6:K8'); // Merge Total Amount
$sheet->mergeCells('L7:L8'); // Merge Climate Change Adaptation
$sheet->mergeCells('L6:M6'); // Merge Climate Change Adaptation
$sheet->mergeCells('M7:M8'); // Merge Climate Change Mitigation
$sheet->mergeCells('N6:N8'); // Merge CC Typology Code

// Apply the same style settings to merged headers
$sheet->getStyle('A6:N8')->applyFromArray([
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFFF00'] // Yellow background
    ],
    'font' => [
        'bold' => true
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

$row = 9;
$total_personal_services = $total_maintenance_expenses = $total_capital_outlay = 0;

foreach ($data as $record) {
    $personal_services = $record['personal_services'] ?? 0;
    $maintenance_expenses = $record['maintenance_expenses'] ?? 0;
    $capital_outlay = $record['capital_outlay'] ?? 0;
    $total_amount = $personal_services + $maintenance_expenses + $capital_outlay;

    $sheet->setCellValue("A$row", $record['aip_ref_code']);
    $sheet->setCellValue("B$row", $record['description']);
    $sheet->setCellValue("C$row", $record['implementing_office']);
    $sheet->setCellValue("D$row", date("M. Y", strtotime($record['start_date'])));
    $sheet->setCellValue("E$row", date("M. Y", strtotime($record['end_date'])));
    $sheet->setCellValue("F$row", $record['description'] ?? '-');
    $sheet->setCellValue("G$row", $record['funding_source'] ?? '-');
    $sheet->setCellValue("H$row", number_format($personal_services, 2));
    $sheet->setCellValue("I$row", number_format($maintenance_expenses, 2));
    $sheet->setCellValue("J$row", number_format($capital_outlay, 2));
    $sheet->setCellValue("K$row", number_format($total_amount, 2));
    $sheet->setCellValue("L$row", number_format($record['climate_adaptation'] ?? 0, 2));
    $sheet->setCellValue("M$row", number_format($record['climate_mitigation'] ?? 0, 2));
    $sheet->setCellValue("N$row", $record['cc_typology_code'] ?? '-');

    $total_personal_services += $personal_services;
    $total_maintenance_expenses += $maintenance_expenses;
    $total_capital_outlay += $capital_outlay;

    $row++;
 
}


$sheet->setCellValue("A$row", 'TOTAL:');
$sheet->setCellValue("H$row", '₱' . number_format($total_personal_services, 2));
$sheet->setCellValue("I$row", '₱' . number_format($total_maintenance_expenses, 2));
$sheet->setCellValue("J$row", '₱' . number_format($total_capital_outlay, 2));
$sheet->setCellValue("K$row", '₱' . number_format($total_personal_services + $total_maintenance_expenses + $total_capital_outlay, 2));

   
$highestRow = $sheet->getHighestRow();

// Apply wrap text to data cells
$sheet->getStyle("A6:N$highestRow")->getAlignment()->setWrapText(true);

// Apply borders to all cells containing data
$highestColumn = $sheet->getHighestColumn();
$sheet->getStyle("A6:$highestColumn$highestRow")->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

$row += 2;
$sheet->setCellValue("B$row", $signatory_one);
$sheet->setCellValue("F$row", $signatory_two);
$sheet->setCellValue("J$row", $signatory_three);
$row++;

$sheet->setCellValue("B$row", 'Prepared by:');
$sheet->setCellValue("F$row", 'Checked by:');
$sheet->setCellValue("J$row", 'Approved by:');
$row++;
$sheet->setCellValue("B$row", 'Date:');
$sheet->setCellValue("F$row", 'Date:');
$sheet->setCellValue("J$row", 'Date:');



$writer = new Xlsx($spreadsheet);
$sheet->getStyle("A6:$highestColumn$highestRow")->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

// Set page orientation to Landscape
$sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

// Set paper size to Legal
$sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);

// Set narrow margins
$sheet->getPageMargins()->setTop(0.25);
$sheet->getPageMargins()->setRight(0.25);
$sheet->getPageMargins()->setLeft(0.25);
$sheet->getPageMargins()->setBottom(0.25);
$sheet->getPageMargins()->setHeader(0);
$sheet->getPageMargins()->setFooter(0);



header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Full_AIP_Report.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
