<?php
// db.php: Database connection details
$host = 'localhost';
$dbName = 'apiit_group_1';
$username = 'root';
$password = '@piit_gr0up_1xx';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; 
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>
