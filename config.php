<?php
$host = 'localhost';
$db   = 'c0950131_ventas'; // Nombre exacto de tu base en DonWeb
$user = 'c0950131_ventas'; // En DonWeb, el usuario suele tener el mismo nombre que la base
$pass = 'ge35veKAne';       // La contraseña que definiste
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
