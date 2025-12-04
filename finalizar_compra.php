<?php
// finalizar_compra.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_usuario = (int) $_SESSION['usuario_id'];


$sql = "SELECT c.id_carrito, c.id_producto, c.cantidad, p.stock
        FROM carrito c
        INNER JOIN producto p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();


if (!$result || $result->num_rows === 0) {
    $stmt->close();
    header("Location: carrito.php");
    exit();
}


$conn->begin_transaction();

try {
    while ($row = $result->fetch_assoc()) {
        $id_producto = (int) $row['id_producto'];
        $cantidad    = (int) $row['cantidad'];
        $stock       = (int) $row['stock'];

        if ($cantidad <= 0) {
            continue;
        }

        if ($cantidad > $stock) {
            $cantidad = $stock; 
        }

        if ($cantidad <= 0) {
            continue;
        }

 
        $sql_hist = "INSERT INTO historial (id_usuario, id_producto, cantidad)
                     VALUES (?, ?, ?)";
        $stmt_hist = $conn->prepare($sql_hist);
        $stmt_hist->bind_param("iii", $id_usuario, $id_producto, $cantidad);
        $stmt_hist->execute();
        $stmt_hist->close();


        $nuevo_stock = $stock - $cantidad;
        $sql_upd = "UPDATE producto
                    SET stock = ?
                    WHERE id_producto = ?";
        $stmt_upd = $conn->prepare($sql_upd);
        $stmt_upd->bind_param("ii", $nuevo_stock, $id_producto);
        $stmt_upd->execute();
        $stmt_upd->close();
    }

    $stmt->close();

    $sql_del = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt_del = $conn->prepare($sql_del);
    $stmt_del->bind_param("i", $id_usuario);
    $stmt_del->execute();
    $stmt_del->close();


    $conn->commit();

    header("Location: historial.php");
    exit();

} catch (Exception $e) {

    $conn->rollback();

    header("Location: carrito.php");
    exit();
}