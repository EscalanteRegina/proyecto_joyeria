<?php
// admin.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'conexion.php';


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = (int) $_SESSION['usuario_id'];


$sql_admin = "SELECT es_admin, nombre FROM usuario WHERE id_usuario = ?";
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


$sql_prod = "SELECT COUNT(*) AS total_productos,
                    SUM(stock) AS total_stock
             FROM producto";
$res_prod = $conn->query($sql_prod);
$datos_prod = $res_prod ? $res_prod->fetch_assoc() : ['total_productos' => 0, 'total_stock' => 0];


$sql_usuarios = "SELECT COUNT(*) AS total_usuarios FROM usuario";
$res_usuarios = $conn->query($sql_usuarios);
$datos_usuarios = $res_usuarios ? $res_usuarios->fetch_assoc() : ['total_usuarios' => 0];


$sql_hist = "SELECT COUNT(*) AS total_registros,
                    COALESCE(SUM(h.cantidad * p.precio), 0) AS total_vendido
             FROM historial h
             INNER JOIN producto p ON h.id_producto = p.id_producto";
$res_hist = $conn->query($sql_hist);
$datos_hist = $res_hist ? $res_hist->fetch_assoc() : ['total_registros' => 0, 'total_vendido' => 0];


$sql_lista = "SELECT id_producto, nombre, precio, stock, activo
              FROM producto
              ORDER BY nombre ASC";
$res_lista = $conn->query($sql_lista);

include 'includes/header.php';
?>

<h1 class="mb-4 admin-titulo">Panel de administración</h1>

<p class="mb-4 text-center admin-bienvenida">
  Hola, <?php echo htmlspecialchars($admin_data['nombre']); ?>. Aquí puedes gestionar los productos e información de la tienda.
</p>

<div class="admin-wrapper">


  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card admin-resumen-card">
        <div class="card-body">
          <h5 class="card-title">Productos</h5>
          <p class="card-text mb-1">
            <strong><?php echo (int)$datos_prod['total_productos']; ?></strong> productos registrados
          </p>
          <p class="card-text text-muted">
            Stock total: <?php echo (int)$datos_prod['total_stock']; ?> piezas
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card admin-resumen-card">
        <div class="card-body">
          <h5 class="card-title">Usuarios</h5>
          <p class="card-text mb-1">
            <strong><?php echo (int)$datos_usuarios['total_usuarios']; ?></strong> cuentas registradas
          </p>
          <p class="card-text text-muted">
            Incluye clientes y administradores.
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card admin-resumen-card">
        <div class="card-body">
          <h5 class="card-title">Ventas</h5>
          <p class="card-text mb-1">
            <strong><?php echo (int)$datos_hist['total_registros']; ?></strong> movimientos en historial
          </p>
          <p class="card-text text-muted">
            Total vendido: $<?php echo number_format($datos_hist['total_vendido'], 2); ?> MXN
          </p>
        </div>
      </div>
    </div>
  </div>


  <div class="card admin-card-lista">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Gestión de productos</h5>

        <a href="admin_producto_nuevo.php" class="btn btn-primary btn-sm">
          Agregar nuevo producto
        </a>
      </div>

      <div class="table-responsive">
        <table class="table align-middle admin-tabla-productos">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Precio</th>
              <th>Stock</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if ($res_lista && $res_lista->num_rows > 0): ?>
              <?php while ($row = $res_lista->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                  <td>$<?php echo number_format($row['precio'], 2); ?></td>
                  <td><?php echo (int)$row['stock']; ?></td>
                  <td>
                    <?php if ((int)$row['activo'] === 1): ?>
                      <span class="badge text-bg-success admin-badge">Activo</span>
                    <?php else: ?>
                      <span class="badge text-bg-secondary admin-badge">Inactivo</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <a href="admin_producto_editar.php?id=<?php echo (int)$row['id_producto']; ?>"
                        class="btn btn-sm btn-outline-primary">
                      Editar
                    </a>

                    <form method="post" action="admin_producto_eliminar.php"
                        class="d-inline-block ms-1"
                        onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                      <input type="hidden" name="id_producto"
                            value="<?php echo (int)$row['id_producto']; ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                         Eliminar
                      </button>
                    </form>
                  </td>

                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted">
                  No hay productos registrados.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3 text-end">
        <a href="historial.php" class="btn btn-outline-secondary btn-sm">
          Ver historial de compras
        </a>
      </div>
    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>
