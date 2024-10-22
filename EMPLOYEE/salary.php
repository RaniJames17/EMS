<?php
session_start(); // Start the session
include('../config/db.php');

// Fetch departments for the dropdown
$departments = [];
try {
    $sql = "SELECT DEPARTMENT_ID, DEPARTMENT_NAME FROM department"; // Adjust the column names as per your table structure
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['message'] = "Error fetching departments: " . $e->getMessage();
    $_SESSION['message_type'] = "alert-danger"; 
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_id = $_POST['department_id'];
    $salary_threshold = $_POST['salary_threshold'];
    $increase_percentage = $_POST['increase_percentage'];

    try {
        // Prepare the call to the stored procedure
        $sql = "CALL Pkg_employee_management.proc_increase_salary(?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Execute the stored procedure
        $stmt->execute([$department_id, $salary_threshold, $increase_percentage]);

        // Show confirmation message
        $_SESSION['message'] = "Employee(s) salary increased successfully.";
        $_SESSION['message_type'] = "alert-success"; 
        header("Location: salary.php"); // Redirect to prevent form resubmission
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "alert-danger"; 
    }
}

// Include the header
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
    input[type="number"], input[type="text"], select {
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
    <h2>Increase Employee Salary</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?php echo htmlentities($_SESSION['message_type']); ?>">
            <?php echo htmlentities($_SESSION['message']); ?>
            <?php unset($_SESSION['message']); // Clear the message after displaying it ?>
            <?php unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form">
        <div class="form-group">
            <label for="department_id">Department:</label>
            <select id="department_id" name="department_id" required>
                <option value="">Select a department</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?php echo htmlentities($department['DEPARTMENT_ID']); ?>">
                        <?php echo htmlentities($department['DEPARTMENT_NAME']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="salary_threshold">Salary Threshold:</label>
            <input type="number" id="salary_threshold" name="salary_threshold" required>
        </div>

        <div class="form-group">
            <label for="increase_percentage">Increase Percentage:</label>
            <input type="number" id="increase_percentage" name="increase_percentage" min="0" max="100" required>
        </div>

        <input type="submit" value="Increase Salary">
    </form>
</div>

<?php include('../partials/footer.php'); ?>
