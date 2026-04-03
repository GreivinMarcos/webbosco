<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

include('../conexion.php');

$usuario_id = $_SESSION['id_usuario'];

// ========================
// AGREGAR PRODUCTO
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {

    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $imagen = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $dir = "../uploads/";
        if (!file_exists($dir)) mkdir($dir, 0777, true);

        $nombreImg = time() . "_" . basename($_FILES["imagen"]["name"]);
        $ruta = $dir . $nombreImg;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta)) {
            $imagen = "uploads/" . $nombreImg;
        }
    }

    $stmt = $conn->prepare("INSERT INTO inventario (nombre, precio, stock, descripcion, imagen, categoria, activo) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sdisss", $nombre, $precio, $stock, $descripcion, $imagen, $categoria);

    if ($stmt->execute()) {

        $producto_id = $stmt->insert_id;

        $mov = $conn->prepare("INSERT INTO inventario_movimientos 
        (producto_id, usuario_id, tipo, cantidad, stock_antes, stock_despues, motivo)
        VALUES (?, ?, 'entrada', ?, 0, ?, ?)");

        $motivo = "Creación de producto";
        $mov->bind_param("iiiis", $producto_id, $usuario_id, $stock, $stock, $motivo);
        $mov->execute();

        header("Location: inventario.php");
        exit();
    }
}

// ========================
// ACTUALIZAR PRODUCTO
// ========================
if (isset($_POST['actualizar'])) {

    $id = intval($_POST['id']);
    $precio = $_POST['precio'];
    $stock_nuevo = intval($_POST['stock']);
    $descripcion = $_POST['descripcion'];

    $res = $conn->query("SELECT stock FROM inventario WHERE id = $id");
    $data = $res->fetch_assoc();

    $stock_antes = $data['stock'];

    $stmt = $conn->prepare("UPDATE inventario SET precio=?, stock=?, descripcion=? WHERE id=?");
    $stmt->bind_param("disi", $precio, $stock_nuevo, $descripcion, $id);
    $stmt->execute();

    $tipo = "ajuste";
    $cantidad = abs($stock_nuevo - $stock_antes);
    $motivo = "Actualización de producto";

    $mov = $conn->prepare("INSERT INTO inventario_movimientos 
    (producto_id, usuario_id, tipo, cantidad, stock_antes, stock_despues, motivo)
    VALUES (?, ?, ?, ?, ?, ?, ?)");

    $mov->bind_param("iisiiis", $id, $usuario_id, $tipo, $cantidad, $stock_antes, $stock_nuevo, $motivo);
    $mov->execute();

    header("Location: inventario.php");
    exit;
}

// ========================
// ACTIVAR / DESACTIVAR
// ========================
if (isset($_GET['toggle'])) {

    $id = intval($_GET['toggle']);

    $res = $conn->query("SELECT activo FROM inventario WHERE id = $id");
    $data = $res->fetch_assoc();

    $nuevo = $data['activo'] ? 0 : 1;

    $conn->query("UPDATE inventario SET activo = $nuevo WHERE id = $id");

    $motivo = $nuevo ? "Producto activado" : "Producto desactivado";

    $mov = $conn->prepare("INSERT INTO inventario_movimientos 
    (producto_id, usuario_id, tipo, cantidad, stock_antes, stock_despues, motivo)
    VALUES (?, ?, 'estado', 0, 0, 0, ?)");

    $mov->bind_param("iis", $id, $usuario_id, $motivo);
    $mov->execute();

    header("Location: inventario.php");
    exit;
}

// ========================
// ELIMINAR PRODUCTO
// ========================
if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']);

    $res = $conn->query("SELECT stock FROM inventario WHERE id = $id");
    $data = $res->fetch_assoc();

    if ($data) {

        $mov = $conn->prepare("INSERT INTO inventario_movimientos 
        (producto_id, usuario_id, tipo, cantidad, stock_antes, stock_despues, motivo)
        VALUES (?, ?, 'salida', ?, ?, 0, ?)");

        $motivo = "Producto eliminado";
        $mov->bind_param("iiiis", $id, $usuario_id, $data['stock'], $data['stock'], $motivo);
        $mov->execute();

        $stmt = $conn->prepare("DELETE FROM inventario WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    header("Location: inventario.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario</title>

<style>
body { background:#0f0f0f; color:#fff; font-family:Poppins; }
header { background:#111; padding:15px; text-align:center; border-bottom:3px solid #00bcd4;}
nav a { color:#00bcd4; margin:0 10px; text-decoration:none;}
table { width:95%; margin:30px auto; background:#1a1a1a; }
th,td { padding:10px; text-align:center; }
th { background:#00bcd4; }
.btn { padding:6px 10px; border:none; border-radius:6px; cursor:pointer; }
.btn-update { background:#4caf50; }
.btn-toggle { background:#ff9800; }
.btn-delete { background:#e53935; }
textarea { width:100%; }
</style>
</head>

<body>

<header>
<h1>📦 Inventario FitBar</h1>
<nav>
<a href="admin_dashboard.php">Inicio</a>
<a href="usuarios.php">Usuarios</a>
<a href="ordenes.php">Órdenes</a>
<a href="../logout.php">Cerrar Sesión</a>
</nav>
</header>

<main style="width:90%; margin:auto;">

<h2>Agregar Producto</h2>
<form method="POST" enctype="multipart/form-data">
<input type="text" name="nombre" placeholder="Nombre" required>
<input type="number" name="precio" placeholder="Precio" required>
<input type="number" name="stock" placeholder="Stock" required>

<select name="categoria" required>
<option>General</option>
<option>Batidos</option>
<option>Snacks</option>
<option>Bowls</option>
<option>Suplementos</option>
<option>Ropa</option>
<option>Adicional</option>
</select>

<textarea name="descripcion" placeholder="Descripción"></textarea>
<input type="file" name="imagen">
<button>Agregar</button>
</form>

<table>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Imagen</th>
<th>Categoría</th>
<th>Precio</th>
<th>Stock</th>
<th>Disponible</th>
<th>Activo</th>
<th>Acciones</th>
</tr>

<?php
$res = $conn->query("
SELECT 
    i.*, 
    COALESCE(SUM(CASE 
        WHEN o.estado = 'completada' THEN c.cantidad 
        ELSE 0 
    END), 0) as vendidos
FROM inventario i
LEFT JOIN carrito c ON c.producto_id = i.id
LEFT JOIN ordenes o ON c.orden_id = o.id
GROUP BY i.id
ORDER BY i.id DESC
");

while($fila = $res->fetch_assoc()){

$disponible = $fila['stock'] - $fila['vendidos'];

// 🔥 AUTO-DESACTIVAR SI LLEGA A 0
if ($disponible <= 0 && $fila['activo'] == 1) {
    $conn->query("UPDATE inventario SET activo = 0 WHERE id = ".$fila['id']);
    $fila['activo'] = 0;
}
?>

<tr>
<td><?= $fila['id'] ?></td>
<td><?= $fila['nombre'] ?></td>

<td>
<?php if($fila['imagen']){ ?>
<img src="../<?= $fila['imagen'] ?>" width="60">
<?php } ?>
</td>

<td><?= $fila['categoria'] ?></td>

<td>
<form method="POST">
<input type="hidden" name="id" value="<?= $fila['id'] ?>">
<input type="number" name="precio" value="<?= $fila['precio'] ?>">
</td>

<td>
<input type="number" name="stock" value="<?= $fila['stock'] ?>">
</td>

<td><?= $disponible ?></td>

<td><?= $fila['activo'] ? 'Sí' : 'No' ?></td>

<td>

<textarea name="descripcion"><?= $fila['descripcion'] ?></textarea>

<button name="actualizar" class="btn btn-update">Guardar</button>
</form>

<a href="?toggle=<?= $fila['id'] ?>" class="btn btn-toggle">
<?= $fila['activo'] ? 'Desactivar' : 'Activar' ?>
</a>

<a href="?eliminar=<?= $fila['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar?')">
Eliminar
</a>

</td>
</tr>

<?php } ?>

</table>

</main>

</body>
</html>