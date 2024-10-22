<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    // Check if password is set
    if (isset($_POST['password'])) {
        $password = $_POST['password'];

        // Prepare query to check for username
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password matches
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect to dashboard or any page
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Password field is missing.";
    }
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
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        display: block;
        margin-bottom: 5px;
        color: #555;
    }
    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type="submit"] {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
    }
    input[type="submit"]:hover {
        background-color: #0056b3;
    }
    .alert {
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
</style>

<div class="container">
    <h2>Login</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
    <?php endif; ?>

    <form action="login.php" method="post" class="form">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <input type="submit" value="Login">
    </form>
</div>

<?php include('../partials/footer.php'); ?>
