<?php
require_once 'db.php';

$price_filter = isset($_GET['birth']) ? htmlspecialchars($_GET['birth']) : '';
$name_filter = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
$description_filter = isset($_GET['description']) ? htmlspecialchars($_GET['description']) : '';
$type_filter = isset($_GET['id_type']) ? htmlspecialchars($_GET['id_type']) : '';

$query = "SELECT * FROM employees";
$where_clauses = array();
$params = array();

if (!empty($price_filter)) {
    $where_clauses[] = "birth LIKE :birth";
    $params[':birth'] = "%$price_filter%";
}

if (!empty($name_filter)) {
    $where_clauses[] = "name LIKE :name";
    $params[':name'] = "%$name_filter%";
}

if (!empty($description_filter)) {
    $where_clauses[] = "description LIKE :description";
    $params[':description'] = "%$description_filter%";
}

if (!empty($type_filter)) {
    $where_clauses[] = "id_type = :id_type";
    $params[':id_type'] = $type_filter;
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt = $connect->prepare($query);
$stmt->execute($params);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtTypes = $connect->query("SELECT * FROM job_title");
$job_title = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
?>