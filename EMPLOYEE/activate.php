<?php
include('../config/db.php');

// Check if the ID is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Prepare the call to the stored procedure
    $sql = "CALL Pkg_employee_management.Proc_status_update(?,'ACTIVE')"; // Adjusted to use the procedure
    $stmt = $pdo->prepare($sql);
    
    // Execute the statement
    if ($stmt->execute([$id])) {
        $message = "Employee marked as ACTIVE.";
    } else {
        $message = "Error: Could not activate employee.";
    }
} else {
    $message = "Error: No employee ID provided.";
}
?>

<?php include('../partials/header.php'); ?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }
    .container {
        max-width: 400px;
        margin: 50px auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
    }
    .alert {
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid transparent;
        border-radius: 4px;
        background-color: #e9ecef;
        color: #333;
    }
    .btn {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    .btn:hover {
        background-color: #0056b3;
    }
</style>

<div class="container">
    <h2>Employee Status</h2>
    
    <?php if (isset($message)): ?>
        <div class="alert"><?php echo htmlentities($message); ?></div>
    <?php endif; ?>
    
    <p>
        <a href="read.php" class="btn">Back to Employee List</a>
    </p>
</div>

<?php include('../partials/footer.php'); ?>
