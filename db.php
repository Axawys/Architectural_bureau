<?php

try {
    $connect = new PDO('mysql:host=db;dbname=appdb;charset=utf8', 'user', 'password');
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
