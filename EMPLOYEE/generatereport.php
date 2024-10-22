<?php
session_start();
include('../config/db.php');
require('../fpdf/fpdf.php'); // Including FPDF

// Start output buffering
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PDF generation starts here
class PDF extends FPDF
{
    // Header
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Employee Report', 0, 1, 'C');
        $this->Ln(20);
    }

    // Footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Table Header
    function headerTable()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, 'Department', 1, 0, 'C');
        $this->Cell(40, 10, 'Employees', 1, 0, 'C');
        $this->Cell(40, 10, 'Min Salary', 1, 0, 'C');
        $this->Cell(40, 10, 'Max Salary', 1, 0, 'C');
        $this->Ln();
    }

    // Table Data
    function viewTable($pdo)
    {
        $this->SetFont('Arial', '', 12);
        try {
            $sql = "SELECT d.DEPARTMENT_NAME,
                           COUNT(e.EMPLOYEE_ID) AS TOTAL_EMPLOYEES,
                           MIN(e.SALARY) AS MIN_SALARY,
                           MAX(e.SALARY) AS MAX_SALARY
                    FROM employee e
                    INNER JOIN department d ON e.DEPARTMENT_ID = d.DEPARTMENT_ID
                    GROUP BY d.DEPARTMENT_NAME";
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if results are empty
            if (empty($results)) {
                $this->Cell(0, 10, 'No data found', 1, 1, 'C');
                return;
            }

            // Loop through results and add them to the PDF
            foreach ($results as $row) {
                // Check if row data is set and is numeric
                $department_name = isset($row['DEPARTMENT_NAME']) ? $row['DEPARTMENT_NAME'] : 'N/A';
                $total_employees = isset($row['TOTAL_EMPLOYEES']) ? $row['TOTAL_EMPLOYEES'] : 'N/A';
                $min_salary = isset($row['MIN_SALARY']) ? $row['MIN_SALARY'] : 'N/A';
                $max_salary = isset($row['MAX_SALARY']) ? $row['MAX_SALARY'] : 'N/A';

                // Format salaries
                $formatted_min_salary = is_numeric($min_salary) ? number_format($min_salary, 2) : 'N/A';
                $formatted_max_salary = is_numeric($max_salary) ? number_format($max_salary, 2) : 'N/A';

                $this->Cell(40, 10, $department_name, 1);
                $this->Cell(40, 10, $total_employees, 1);
                $this->Cell(40, 10, $formatted_min_salary, 1);
                $this->Cell(40, 10, $formatted_max_salary, 1);
                $this->Ln();
            }
        } catch (PDOException $e) {
            $this->Cell(0, 10, 'Error: ' . $e->getMessage(), 1, 1, 'C');
        }
    }
}

try {
    // Create PDF instance and add a page
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->headerTable();
    $pdf->viewTable($pdo);
    
    // Debug: Check if there's any output before PDF
    $output = ob_get_contents();
    if (!empty($output)) {
        error_log("Output before PDF generation: " . $output);
    }

    // Output the PDF
    $pdf->Output('D', 'Employee_Report.pdf'); // Force download as 'Employee_Report.pdf'
    ob_end_clean(); // Clean output buffer
    exit(); // Exit to prevent further rendering of HTML
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
    ob_end_flush(); // Flush output buffer
}
?>
