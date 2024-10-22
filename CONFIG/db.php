<?php
// Oracle connection credentials
$dsn = 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=MY_PDB)))';
$username = 'EMS'; // Connect as SYSDBA
$password = 'ems'; // Replace with your Oracle DB password

try {
    // Establish a connection using PDO
    $pdo = new PDO($dsn, $username, $password);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, display the error
    echo "Connection failed: " . htmlentities($e->getMessage(), ENT_QUOTES);
}
?>
