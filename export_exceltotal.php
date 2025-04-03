<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Column Widths (Manually Adjustable)
$columnWidths = [
    'A' => 20,  // AIP Ref Code
    'B' => 20,  // Department Office
    'C' => 18,  // Personal Services
    'D' => 21,  // Maintenance Expenses
    'E' => 18,  // Capital Outlay
    'F' => 18,  // Total Expenses
    'G' => 26,  // Climate Change Adaptation
    'H' => 24   // Climate Change Mitigation
];

foreach ($columnWidths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}

// Set Header
$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', 'CEBU CITY ANNUAL INVESTMENT PROGRAM CY 2025');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Second Header Row
$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A2', 'General Fund Proper');
$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9E1F2');

// Set Table Headers
$headers = ['A3' => 'AIP Ref Code', 'B3' => 'Department Office', 'C3' => 'Personal Services',
    'D3' => 'Maintenance Expenses', 'E3' => 'Capital Outlay', 'F3' => 'Total Expenses',
    'G3' => 'Climate Change Adaptation', 'H3' => 'Climate Change Mitigation'];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
    $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('1F4E78');
}

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// Set Page Orientation to Landscape
$sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

// Set Paper Size to Legal
$sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);

// Set Narrow Margins
$sheet->getPageMargins()->setTop(0.25);
$sheet->getPageMargins()->setRight(0.25);
$sheet->getPageMargins()->setLeft(0.25);
$sheet->getPageMargins()->setBottom(0.25);

// Set Borders
$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];

// Sample Data
$data = [
    ["Institutional Development Sector", "AIP-6000-000-2-4-05", "Management Information and Computer Services", 530000, 25230000, 109800000, 135560000, 0, 0],
    ["Social Sector", "AIP-8000-000-2-7-50", "Cebu City Sports Commission (CCSC)", 5843000, 53440700, 8030000, 67313700, 0, 0]
];

$row = 4;
$current_sector = null;
$sector_totals = [0, 0, 0, 0, 0, 0];
$overall_totals = [0, 0, 0, 0, 0, 0];

foreach ($data as $record) {
    [$sector, $aip_ref, $department, $personal, $maintenance, $capital, $total, $climate_adaptation, $climate_mitigation] = $record;

    if ($current_sector != $sector) {
        if ($current_sector !== null) {
            // Print Grand Total for Sector
            $sheet->setCellValue("A$row", "Grand Total:");
            $sheet->mergeCells("A$row:B$row");
            $sheet->getStyle("A$row")->getFont()->setBold(true);
            $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach (range('C', 'H') as $idx => $col) {
                $cell = $col . $row;
                $sheet->setCellValue($cell, '₱' . number_format($sector_totals[$idx], 2));
                $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            $sheet->getStyle("A$row:H$row")->applyFromArray($borderStyle);
            $row++;
            $sector_totals = [0, 0, 0, 0, 0, 0];
        }

        // New Sector Header
        $current_sector = $sector;
        $sheet->setCellValue("A$row", $current_sector);
        $sheet->mergeCells("A$row:H$row");
        $sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("A$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9E1F2');
        $row++;
    }

    // Insert Data
    $sheet->setCellValue("A$row", $aip_ref);
    $sheet->setCellValue("B$row", $department);
    $sheet->setCellValue("C$row", '₱' . number_format($personal, 2));
    $sheet->setCellValue("D$row", '₱' . number_format($maintenance, 2));
    $sheet->setCellValue("E$row", '₱' . number_format($capital, 2));
    $sheet->setCellValue("F$row", '₱' . number_format($total, 2));
    $sheet->setCellValue("G$row", '₱' . number_format($climate_adaptation, 2));
    $sheet->setCellValue("H$row", '₱' . number_format($climate_mitigation, 2));

    // **Wrap Text for Data Cells**
    $sheet->getStyle("A$row:H$row")->getAlignment()->setWrapText(true);

    $sector_totals = array_map(fn($a, $b) => $a + $b, $sector_totals, [$personal, $maintenance, $capital, $total, $climate_adaptation, $climate_mitigation]);
    $overall_totals = array_map(fn($a, $b) => $a + $b, $overall_totals, [$personal, $maintenance, $capital, $total, $climate_adaptation, $climate_mitigation]);

    $sheet->getStyle("A$row:H$row")->applyFromArray($borderStyle);
    $row++;
}

// Print Final Grand Total
$sheet->setCellValue("A$row", "Total Annual Investment Program:");
$sheet->mergeCells("A$row:B$row");
foreach (range('C', 'H') as $idx => $col) {
    $sheet->setCellValue("$col$row", '₱' . number_format($overall_totals[$idx], 2));
    $sheet->getStyle("$col$row")->getFont()->setBold(true);
}
$sheet->getStyle("A$row:H$row")->applyFromArray($borderStyle);

// Output File
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Formatted_AIP_Report.xlsx"');
$writer->save('php://output');
exit;
