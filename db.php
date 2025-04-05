<?php
$dsn = 'mysql:host=localhost;dbname=arch;charset=utf8';
$username = 'root';
$password = '';

try {
    $connect = new PDO('mysql:host=localhost;dbname=arch;charset=utf8', 'root', '');
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
