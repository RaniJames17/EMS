<?php include('../config/db.php'); ?>
<?php include('../partials/header.php'); ?>



<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .content {
        max-width: 100%;
        margin: 0 auto;
        background: #ffffff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }
    .filter-container {
        margin-bottom: 20px;
        text-align: center;
    }
    .filter-container form {
        display: inline-block;
    }
    .filter-container select, .filter-container input {
        padding: 8px;
        margin-right: 10px;
    }
    .table-container {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-top: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        padding: 15px;
        text-align: left;
        border: 1px solid #dddddd;
    }
    th {
        background-color: #007bff;
        color: white;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #e9ecef;
    }
    a {
        text-decoration: none;
        color: #007bff;
    }
    a:hover {
        text-decoration: underline;
    }
    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    .back-button {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
        text-align: center;
        margin-top: 20px;
    }
    .back-button:hover {
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

<div class="content">
    <h2>Employee List</h2>
    
    <!-- Display the session message if it exists -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?php echo htmlentities($_SESSION['message_type']); ?>">
            <?php
            echo htmlentities($_SESSION['message']); 
            unset($_SESSION['message']); // Remove message after displaying
            ?>
        </div>
    <?php endif; ?>

    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="">
            <label for="department">Department:</label>
            <select name="department" id="department">
                <option value="">All</option>
                <?php
                // Query to get distinct departments
                $deptSql = "SELECT DISTINCT DEPARTMENT_ID FROM employee WHERE DEPARTMENT_ID IS NOT NULL";
                $deptStmt = $pdo->prepare($deptSql);
                $deptStmt->execute();

                // Populate department options
                while ($row = $deptStmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = (isset($_GET['department']) && $_GET['department'] == $row['DEPARTMENT_ID']) ? 'selected' : '';
                    echo "<option value='" . htmlentities($row['DEPARTMENT_ID']) . "' $selected>" . htmlentities($row['DEPARTMENT_ID']) . "</option>";
                }
                ?>
            </select>

            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">All</option>
                <option value="Active" <?php if (isset($_GET['status']) && $_GET['status'] == 'Active') echo 'selected'; ?>>Active</option>
                <option value="Inactive" <?php if (isset($_GET['status']) && $_GET['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
            </select>

            <label for="job_id">Job ID:</label>
            <select name="job_id" id="job_id">
                <option value="">All</option>
                <?php
                // Query to get distinct job IDs
                $jobSql = "SELECT DISTINCT JOB_ID FROM employee WHERE JOB_ID IS NOT NULL";
                $jobStmt = $pdo->prepare($jobSql);
                $jobStmt->execute();

                // Populate job ID options
                while ($row = $jobStmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = (isset($_GET['job_id']) && $_GET['job_id'] == $row['JOB_ID']) ? 'selected' : '';
                    echo "<option value='" . htmlentities($row['JOB_ID']) . "' $selected>" . htmlentities($row['JOB_ID']) . "</option>";
                }
                ?>
            </select>

            <label for="sort_by">Sort By:</label>
            <select name="sort_by" id="sort_by">
                <option value="first_name" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'first_name') echo 'selected'; ?>>First Name</option>
                <option value="department_id" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'department_id') echo 'selected'; ?>>Department</option>
                <option value="hire_date" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'hire_date') echo 'selected'; ?>>Hire Date</option>
            </select>

            <button type="submit" class="back-button">Filter & Sort</button>
        </form>
    </div>

    <div class="table-container">
        <?php
        try {
            // Base SQL query
            $sql = "
                SELECT e.EMPLOYEE_ID, e.FIRST_NAME, e.LAST_NAME, e.EMAIL, e.PHONE_NUMBER, e.HIRE_DATE, e.JOB_ID, e.SALARY, d.DEPARTMENT_NAME, e.STATUS 
                FROM employee e 
                JOIN department d ON e.DEPARTMENT_ID = d.DEPARTMENT_ID 
                WHERE 1=1
            ";

            // Add filters if provided
            $filters = [];
            if (!empty($_GET['department'])) {
                $sql .= " AND e.DEPARTMENT_ID = :department";
                $filters[':department'] = $_GET['department'];
            }
            if (!empty($_GET['status'])) {
                $sql .= " AND upper(e.STATUS) = upper(:status)";
                $filters[':status'] = $_GET['status'];
            }
            if (!empty($_GET['job_id'])) {
                $sql .= " AND e.JOB_ID = :job_id";
                $filters[':job_id'] = $_GET['job_id'];
            }

            // Sorting
            $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'first_name';
            $sql .= " ORDER BY ";
            if ($sort_by === 'first_name') {
                $sql .= "e.FIRST_NAME";
            } elseif ($sort_by === 'department_id') {
                $sql .= "d.DEPARTMENT_NAME";
            } elseif ($sort_by === 'hire_date') {
                $sql .= "e.HIRE_DATE";
            }

            $stmt = $pdo->prepare($sql);

            // Bind filter values to the query
            foreach ($filters as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            // Execute the query
            $stmt->execute();

            // Display the results in an HTML table
            echo "<table>";
            echo "<tr>
                    <th>Employee ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Hire Date</th>
                    <th>Job Title</th>
                    <th>Salary</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>";

            // Fetch and display each employee record
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlentities($row['EMPLOYEE_ID']) . "</td>";
                echo "<td>" . htmlentities($row['FIRST_NAME']) . "</td>";
                echo "<td>" . htmlentities($row['LAST_NAME']) . "</td>";
                echo "<td>" . htmlentities($row['EMAIL']) . "</td>";
                echo "<td>" . htmlentities($row['PHONE_NUMBER']) . "</td>";
                echo "<td>" . htmlentities($row['HIRE_DATE']) . "</td>";
                echo "<td>" . htmlentities($row['JOB_ID']) . "</td>";
                echo "<td>" . htmlentities($row['SALARY']) . "</td>";
                echo "<td>" . htmlentities($row['DEPARTMENT_NAME']) . "</td>";
                echo "<td>" . htmlentities($row['STATUS']) . "</td>";
                echo "<td class='action-buttons'>";
                
                // Display appropriate action buttons based on employee status
                if (strtoupper($row['STATUS']) == 'INACTIVE') {
                    echo "<a href='activate.php?id=" . htmlentities($row['EMPLOYEE_ID']) . "'>Activate</a>";
                } else {
                    echo "<a href='update.php?id=" . htmlentities($row['EMPLOYEE_ID']) . "'>Edit</a> | 
                          <a href='delete.php?id=" . htmlentities($row['EMPLOYEE_ID']) . "'>Delete</a>";
                }
                
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";

        } catch (PDOException $e) {
            // Output the error details
            echo "Error: " . htmlentities($e->getMessage()) . "<br/>";
            echo "Error Code: " . htmlentities($e->getCode());
            echo "<br/>Trace: " . htmlentities($e->getTraceAsString());
        }
        ?>
    </div>
</div>

<?php include('../partials/footer.php'); ?>
