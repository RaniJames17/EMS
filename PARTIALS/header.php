<!DOCTYPE html>
<html>
<head>
    <title>Employee Management</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 0;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        nav {
            margin-top: 10px;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Employee Management System</h1>
        <nav>
            <a href="/EMS/index.php">Home</a>
            <a href="/EMS/employee/create.php">Add Employee</a>
            <a href="/EMS/employee/read.php">View Employees</a>
            <a href="/EMS/employee/salary.php">Modify Dept Salary</a>
            <a href="/EMS/employee/generatereport.php">Generate Reports</a>
        </nav>
    </header>
</body>
</html>
