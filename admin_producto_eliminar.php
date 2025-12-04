<?php
// admin_producto_eliminar.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = (int) $_SESSION['usuario_id'];

// Verificar que sea admin
$sql_admin = "SELECT es_admin FROM usuario WHERE id_usuario = ?";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param("i", $id_usuario);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();

if (!$result_admin || $result_admin->num_rows === 0) {
    $stmt_admin->close();
    header("Location: index.php");
    exit();
}

$admin_data = $result_admin->fetch_assoc();
$stmt_admin->close();

if ((int)$admin_data['es_admin'] !== 1) {
    header("Location: index.php");
    exit();
}

// Verificar que venga por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit();
}

$id_producto = isset($_POST['id_producto']) ? (int) $_POST['id_producto'] : 0;

if ($id_producto <= 0) {
    header("Location: admin.php");
    exit();
}

// Opcional: podrías verificar que exista el producto antes de borrar

// Eliminar producto
$sql_del = "DELETE FROM producto WHERE id_producto = ?";
$stmt_del = $conn->prepare($sql_del);
$stmt_del->bind_param("i", $id_producto);
$stmt_del->execute();
$stmt_del->close();

// Gracias a ON DELETE CASCADE en carrito/historial,
// también se eliminan sus registros relacionados.

// Volver al panel admin
header("Location: admin.php");
exit();