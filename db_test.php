<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=kathaingo', 'ilang', '');
    echo "Connected successfully\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
