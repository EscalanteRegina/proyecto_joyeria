<?php
// finalizar_compra.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay usuario en sesión, mandamos a login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_usuario = (int) $_SESSION['usuario_id'];

// 1. Obtener los productos del carrito de este usuario
$sql = "SELECT c.id_carrito, c.id_producto, c.cantidad, p.stock
        FROM carrito c
        INNER JOIN producto p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Si el carrito está vacío, regresamos al carrito
if (!$result || $result->num_rows === 0) {
    $stmt->close();
    header("Location: carrito.php");
    exit();
}

// Opcional: iniciar transacción para que todo sea consistente
$conn->begin_transaction();

try {
    while ($row = $result->fetch_assoc()) {
        $id_producto = (int) $row['id_producto'];
        $cantidad    = (int) $row['cantidad'];
        $stock       = (int) $row['stock'];

        if ($cantidad <= 0) {
            continue;
        }

        // Evitar que el stock quede negativo
        if ($cantidad > $stock) {
            $cantidad = $stock; // en un proyecto real podrías mostrar aviso
        }

        if ($cantidad <= 0) {
            continue;
        }

        // 2. Insertar en historial
        $sql_hist = "INSERT INTO historial (id_usuario, id_producto, cantidad)
                     VALUES (?, ?, ?)";
        $stmt_hist = $conn->prepare($sql_hist);
        $stmt_hist->bind_param("iii", $id_usuario, $id_producto, $cantidad);
        $stmt_hist->execute();
        $stmt_hist->close();

        // 3. Actualizar stock en producto
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

    // 4. Vaciar carrito del usuario
    $sql_del = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt_del = $conn->prepare($sql_del);
    $stmt_del->bind_param("i", $id_usuario);
    $stmt_del->execute();
    $stmt_del->close();

    // Confirmar transacción
    $conn->commit();

    // Redirigimos al historial (o a una página de "gracias")
    header("Location: historial.php");
    exit();

} catch (Exception $e) {
    // En caso de error, revertir cambios
    $conn->rollback();
    // Puedes redirigir al carrito con un mensaje de error si quieres
    header("Location: carrito.php");
    exit();
}