<?php
// agregar_carrito.php
require 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario_id'])) {

    header("Location: login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id_producto'])) {
    header("Location: catalogo.php");
    exit();
}

$id_usuario  = (int) $_SESSION['usuario_id'];
$id_producto = (int) $_POST['id_producto'];

if ($id_producto <= 0) {
    header("Location: catalogo.php");
    exit();
}


$sql  = "SELECT cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_producto);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {

    $stmt->bind_result($cantidad_actual);
    $stmt->fetch();
    $stmt->close();

    $nueva_cantidad = $cantidad_actual + 1;

    $sql_update = "UPDATE carrito 
                   SET cantidad = ? 
                   WHERE id_usuario = ? AND id_producto = ?";
    $stmt2 = $conn->prepare($sql_update);
    $stmt2->bind_param("iii", $nueva_cantidad, $id_usuario, $id_producto);
    $stmt2->execute();
    $stmt2->close();

} else {

    $stmt->close();

    $cantidad_inicial = 1;
    $sql_insert = "INSERT INTO carrito (id_usuario, id_producto, cantidad)
                   VALUES (?, ?, ?)";
    $stmt2 = $conn->prepare($sql_insert);
    $stmt2->bind_param("iii", $id_usuario, $id_producto, $cantidad_inicial);
    $stmt2->execute();
    $stmt2->close();
}

header("Location: carrito.php");
exit();