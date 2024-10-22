<?php
session_start(); // Start the session
include('../config/db.php');




// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id']; // Use EMPLOYEE_ID instead of id
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $job_id = $_POST['job_id'];
    $salary = $_POST['salary'];
    $department_id = $_POST['department_id'];

    // Call the stored procedure
    $sql = "CALL Pkg_employee_management.proc_update_employee(?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Execute the stored procedure
    if ($stmt->execute([$id, $first_name, $last_name, $email, $phone_number, $job_id, $salary, $department_id])) {
        
        // Show confirmation message
        echo "<script>
            if (confirm('Employee updated successfully! Do you want to make more changes?')) {
                // Stay on the same page
                window.location.href = window.location.href; // Reload the page
            } else {
                // Redirect to read.php
                window.location.href = 'read.php';
            }
        </script>";
        exit(); // Make sure to exit after the redirection
    } else {
        $_SESSION['message'] = "Error: Could not update employee.";
        $_SESSION['message_type'] = "alert-danger"; 
        // Stay on the same page and show error message
    }
}

// Get employee ID from query parameter
$id = $_GET['id'];
$sql = "SELECT * FROM employee WHERE EMPLOYEE_ID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the employee exists
if (!$employee) {
    echo "Error: Employee not found.";
    exit;
}

include('../partials/header.php'); 
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }
    .container {
        max-width: 600px;
        margin: 50px auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        display: block;
        margin-bottom: 5px;
        color: #555;
    }
    input[type="text"], input[type="email"], input[type="number"], select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type="submit"], button {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }
    input[type="submit"]:hover, button:hover {
        background-color: #0056b3;
    }
    .alert {
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-success {
        background-color: #d4edda; /* Green background */
        color: #155724; /* Dark green text */
        border-color: #c3e6cb; /* Light green border */
    }
    .alert-danger {
        background-color: #f8d7da; /* Red background */
        color: #721c24; /* Dark red text */
        border-color: #f5c6cb; /* Light red border */
    }
</style>

<div class="container">
    <h2>Edit Employee</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?php echo htmlentities($_SESSION['message_type']); ?>">
            <?php echo htmlentities($_SESSION['message']); ?>
            <?php unset($_SESSION['message']); // Clear the message after displaying it ?>
            <?php unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form">
        <input type="hidden" name="id" value="<?php echo htmlentities($employee['EMPLOYEE_ID']); ?>">
        
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlentities($employee['FIRST_NAME']); ?>" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlentities($employee['LAST_NAME']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlentities($employee['EMAIL']); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlentities($employee['PHONE_NUMBER']); ?>" required>
        </div>

        <div class="form-group">
            <label for="job_id">Job ID:</label>
            <input type="text" id="job_id" name="job_id" value="<?php echo htmlentities($employee['JOB_ID']); ?>" required>
        </div>

        <div class="form-group">
            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" value="<?php echo htmlentities($employee['SALARY']); ?>" required>
        </div>

        <div class="form-group">
            <label for="department_id">Department ID:</label>
            <input type="text" id="department_id" name="department_id" value="<?php echo htmlentities($employee['DEPARTMENT_ID']); ?>" required>
        </div>

        <input type="submit" value="Update Employee">
    </form>
</div>

<?php include('../partials/footer.php'); ?>
