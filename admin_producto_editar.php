<?php
// admin_producto_editar.php

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

// Obtener ID de producto desde la URL
$id_producto = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id_producto <= 0) {
    header("Location: admin.php");
    exit();
}

$errores = "";
$exito   = "";

// Obtener categorías
$sql_cat = "SELECT id_categoria, nombre 
            FROM categorias
            ORDER BY nombre ASC";
$res_cat = $conn->query($sql_cat);
$categorias = [];
if ($res_cat && $res_cat->num_rows > 0) {
    while ($row_cat = $res_cat->fetch_assoc()) {
        $categorias[] = $row_cat;
    }
}

// Obtener datos actuales del producto
$sql_prod = "SELECT id_producto, nombre, descripcion, precio, stock, imagen, id_categoria, activo
             FROM producto
             WHERE id_producto = ?";
$stmt_prod = $conn->prepare($sql_prod);
$stmt_prod->bind_param("i", $id_producto);
$stmt_prod->execute();
$res_prod = $stmt_prod->get_result();

if (!$res_prod || $res_prod->num_rows === 0) {
    $stmt_prod->close();
    header("Location: admin.php");
    exit();
}

$producto = $res_prod->fetch_assoc();
$stmt_prod->close();

// Inicializar variables para el formulario
$nombre       = $producto['nombre'];
$descripcion  = $producto['descripcion'];
$precio       = $producto['precio'];
$stock        = $producto['stock'];
$imagen       = $producto['imagen'];
$id_categoria = $producto['id_categoria'];
$activo       = (int)$producto['activo'];

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre       = trim($_POST['nombre'] ?? '');
    $descripcion  = trim($_POST['descripcion'] ?? '');
    $precio       = trim($_POST['precio'] ?? '');
    $stock        = trim($_POST['stock'] ?? '');
    $imagen       = trim($_POST['imagen'] ?? '');
    $id_categoria = (int) ($_POST['id_categoria'] ?? 0);
    $activo       = isset($_POST['activo']) ? 1 : 0;

    if ($nombre === "" || $precio === "" || $stock === "" || $id_categoria <= 0) {
        $errores = "Nombre, precio, stock y categoría son obligatorios.";
    } else {
        if (!is_numeric($precio) || !is_numeric($stock)) {
            $errores = "Precio y stock deben ser valores numéricos.";
        } else {
            $precio = (float) $precio;
            $stock  = (int) $stock;

            $sql_upd = "UPDATE producto
                        SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?, id_categoria = ?, activo = ?
                        WHERE id_producto = ?";
            $stmt_upd = $conn->prepare($sql_upd);
            $stmt_upd->bind_param("ssdisiii",
                $nombre,
                $descripcion,
                $precio,
                $stock,
                $imagen,
                $id_categoria,
                $activo,
                $id_producto
            );

            if ($stmt_upd->execute()) {
                $exito = "Producto actualizado correctamente.";
            } else {
                $errores = "Error al actualizar el producto.";
            }

            $stmt_upd->close();
        }
    }
}

include 'includes/header.php';
?>

<h1 class="mb-4 text-center">Editar producto</h1>

<div class="admin-wrapper">
  <div class="card admin-card-lista mb-4">
    <div class="card-body">

      <?php if ($errores != ""): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errores); ?></div>
      <?php endif; ?>

      <?php if ($exito != ""): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($exito); ?></div>
      <?php endif; ?>

      <form method="post" action="admin_producto_editar.php?id=<?php echo (int)$id_producto; ?>">

        <div class="mb-3">
          <label class="form-label">Nombre del producto*</label>
          <input type="text" name="nombre" class="form-control"
                 value="<?php echo htmlspecialchars($nombre); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($descripcion); ?></textarea>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Precio*</label>
            <input type="text" name="precio" class="form-control"
                   value="<?php echo htmlspecialchars($precio); ?>">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Stock*</label>
            <input type="number" name="stock" class="form-control"
                   value="<?php echo htmlspecialchars($stock); ?>">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Categoría*</label>
            <select name="id_categoria" class="form-select">
              <option value="">Selecciona una categoría</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo (int)$cat['id_categoria']; ?>"
                  <?php if ($id_categoria == (int)$cat['id_categoria']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($cat['nombre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Nombre de la imagen (archivo)</label>
          <input type="text" name="imagen" class="form-control"
                 value="<?php echo htmlspecialchars($imagen); ?>">
          <div class="form-text">
            El archivo debe existir en la carpeta <code>img/</code> del proyecto.
          </div>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="activo" id="activo"
                 <?php if ($activo == 1) echo 'checked'; ?>>
          <label class="form-check-label" for="activo">
            Producto activo (visible en el catálogo)
          </label>
        </div>

        <div class="d-flex justify-content-between">
          <a href="admin.php" class="btn btn-outline-secondary">Volver al panel</a>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>