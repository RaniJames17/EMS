<?php 
session_start(); // Start the session
include('../config/db.php');

$message = ""; // Initialize message variable
$message_type = ""; // Initialize message type variable for CSS styling

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    
    // Ensure this is in 'YYYY-MM-DD' format and create DateTime object
    $hire_date = DateTime::createFromFormat('Y-m-d', $_POST['hire_date']);
    $hire_date = $hire_date ? $hire_date->format('Y-m-d H:i:s') : null; // Format as needed
    
    $job_id = trim($_POST['job_id']);
    $salary = floatval(trim($_POST['salary'])); // Ensure salary is treated as a float
    $department_id = intval(trim($_POST['department_id'])); // Ensure department ID is treated as an integer

    // Validate inputs
    if ($salary < 30000 || $salary > 200000) { // Salary range validation
        $_SESSION['message'] = "Salary must be between 30,000 and 200,000.";
        $_SESSION['message_type'] = "alert-danger"; // Red for warnings
    } else {
        // Check if the email already exists
        $emailCheckSql = "SELECT COUNT(*) FROM employee WHERE EMAIL = ?";
        $emailCheckStmt = $pdo->prepare($emailCheckSql);
        $emailCheckStmt->execute([$email]);

        if ($emailCheckStmt->fetchColumn() > 0) {
            $_SESSION['message'] = "This email is already in use.";
            $_SESSION['message_type'] = "alert-danger"; // Red for warnings
        } else {
            // Validate if the department ID exists
            $deptCheckSql = "SELECT COUNT(*) FROM department WHERE DEPARTMENT_ID = ?";
            $deptCheckStmt = $pdo->prepare($deptCheckSql);
            $deptCheckStmt->execute([$department_id]);

            if ($deptCheckStmt->fetchColumn() == 0) {
                $_SESSION['message'] = "Invalid department ID.";
                $_SESSION['message_type'] = "alert-danger"; // Red for warnings
            } else {
                // Prepare to call the stored procedure
                $sql = "BEGIN Pkg_employee_management.proc_add_employee(:first_name, :last_name, :email, :phone_number, TO_DATE(:hire_date, 'YYYY-MM-DD HH24:MI:SS'), :job_id, :salary, :department_id); END;";
                $stmt = $pdo->prepare($sql);

                // Bind parameters
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone_number', $phone_number);
                $stmt->bindParam(':hire_date', $hire_date); // Bind the formatted date string
                $stmt->bindParam(':job_id', $job_id);
                $stmt->bindParam(':salary', $salary);
                $stmt->bindParam(':department_id', $department_id);

                // Execute the stored procedure
                if ($stmt->execute()) {
                    $_SESSION['message'] = "New employee created successfully!";
                    $_SESSION['message_type'] = "alert-success"; // Green for success
                } else {
                    $_SESSION['message'] = "Error: Could not create employee.";
                    $_SESSION['message_type'] = "alert-danger"; // Red for warnings
                }
            }
        }
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch department IDs for the dropdown
$deptSql = "SELECT DEPARTMENT_ID, DEPARTMENT_NAME FROM department";
$deptStmt = $pdo->prepare($deptSql);
$deptStmt->execute();
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

// Check for session messages to display
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']); // Clear message after displaying
    unset($_SESSION['message_type']); // Clear message type after displaying
}
?>

<?php include('../partials/header.php'); ?>

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
    input[type="text"], input[type="email"], input[type="date"], input[type="number"] {
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
    <h2>Add Employee</h2>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo htmlentities($message_type); ?>"><?php echo htmlentities($message); ?></div>
    <?php endif; ?>

    <form method="post" class="form">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>
        </div>

        <div class="form-group">
            <label for="hire_date">Hire Date:</label>
            <input type="date" id="hire_date" name="hire_date" required>
        </div>

        <div class="form-group">
            <label for="job_id">Job ID:</label>
            <input type="text" id="job_id" name="job_id" required>
        </div>

        <div class="form-group">
            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" required>
        </div>

        <div class="form-group">
    <label for="department_id">Department ID:</label>
    <select id="department_id" name="department_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        <option value="">Select Department</option>
        <?php foreach ($departments as $department): ?>
            <option value="<?php echo htmlentities($department['DEPARTMENT_ID']); ?>">
                <?php echo htmlentities($department['DEPARTMENT_NAME']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

        <button type="submit">Add Employee</button>
    </form>
</div>

<?php include('../partials/footer.php'); ?>
