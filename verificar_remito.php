<?php
require_once("config.php");

$remito = $_GET['remito'] ?? '';
$duplicado = false;

if (!empty($remito)) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ventas WHERE remito = ?");
    $stmt->execute([$remito]);
    $duplicado = $stmt->fetchColumn() > 0;
}

header("Content-Type: application/json");
echo json_encode(["duplicado" => $duplicado]);
