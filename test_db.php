<?php
echo "Testing MySQL connection...\n";

try {
    $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    echo "✓ Connected to MySQL!\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS 3413_amikomeventticket");
    echo "✓ Database created/exists!\n";
    
    // Now connect to the specific database
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=3413_amikomeventticket', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "✓ Connected to 3413_amikomeventticket database!\n";
    
    // Show tables
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll();
    
    if (empty($tables)) {
        echo "⚠ No tables found. Need to run migrations.\n";
    } else {
        echo "✓ Found " . count($tables) . " tables\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone!\n";
?>
