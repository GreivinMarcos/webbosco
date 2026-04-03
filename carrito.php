<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['id_usuario'];

// 🔥 OBTENER SI PUEDE FIADO
$sql_user = "SELECT puede_fiado FROM usuarios WHERE id=?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_usuario);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$user_data = $res_user->fetch_assoc();

$puede_fiado = $user_data['puede_fiado'] ?? 0;

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM carrito 
                  WHERE id = $id 
                  AND usuario = '$usuario' 
                  AND finalizado = 0");
    header("Location: carrito.php");
    exit;
}

// FINALIZAR COMPRA
if (isset($_POST['finalizar'])) {

    $tipo_pago = $_POST['tipo_pago'] ?? '';
    $usar_fiado = isset($_POST['fiado']) ? 1 : 0;

    $comprobante = null;
    $fecha_pago = null;

    if ($usar_fiado == 1 && $puede_fiado == 1) {
        $tipo_pago = 'fiado';
    } else {

        if ($tipo_pago === 'sinpe' || $tipo_pago === 'transferencia') {

            if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== 0) {
                echo "<script>alert('Debes subir el comprobante');window.location='carrito.php';</script>";
                exit;
            }

            $dir = "comprobantes/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $nombreArchivo = time() . "_" . basename($_FILES['comprobante']['name']);
            $rutaCompleta = $dir . $nombreArchivo;

            if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaCompleta)) {
                echo "<script>alert('Error al subir comprobante');window.location='carrito.php';</script>";
                exit;
            }

            $comprobante = $rutaCompleta;
            $fecha_pago = date("Y-m-d H:i:s");
        }
    }

    $result = $conn->query("SELECT * FROM carrito 
                           WHERE usuario = '$usuario' 
                           AND finalizado = 0");

    if ($result->num_rows > 0) {

        $total = 0;
        $productos = "";

        while ($fila = $result->fetch_assoc()) {
            $subtotal = $fila['precio'] * $fila['cantidad'];
            $total += $subtotal;
            $productos .= "{$fila['nombre']} (x{$fila['cantidad']}) - ₡" 
                        . number_format($subtotal, 2) . " | ";
        }

        $sql = "INSERT INTO ordenes 
        (usuario_id, id_usuario, producto, total, tipo_pago, comprobante, fecha_pago, fiado, pagado, finalizado, estado, fecha_orden)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 'pendiente', NOW())";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("iisdsssi",
            $id_usuario,
            $id_usuario,
            $productos,
            $total,
            $tipo_pago,
            $comprobante,
            $fecha_pago,
            $usar_fiado
        );

        if (!$stmt->execute()) {
            die("Error al guardar orden: " . $stmt->error);
        }

        $orden_id = $stmt->insert_id;

        $conn->query("UPDATE carrito 
              SET finalizado = 1, orden_id = $orden_id
              WHERE usuario = '$usuario' 
              AND finalizado = 0");

        header("Location: finalizar_orden.php");
        exit;
    }
}

// 🔥 CONSULTA GENERAL
$resultado = $conn->query("
    SELECT c.*, o.estado
    FROM carrito c
    LEFT JOIN ordenes o ON c.orden_id = o.id
    WHERE c.usuario = '$usuario'
");

// 🔥 HISTORIAL
$historial = $conn->query("
    SELECT c.*, o.estado, o.fecha_orden
    FROM carrito c
    LEFT JOIN ordenes o ON c.orden_id = o.id
    WHERE c.usuario = '$usuario' AND c.finalizado = 1
    ORDER BY o.fecha_orden DESC
");

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carrito - BoscoBox</title>

<style>
body { background:#0f0f0f; color:#fff; font-family:Poppins; }
header { background:#111; padding:15px; text-align:center; }
table { width:80%; margin:40px auto; background:#1a1a1a; }
th,td { padding:15px; text-align:center; }
img { width:80px; }
.btn { background:#00bcd4; padding:8px 15px; border:none; border-radius:8px; }
.total { text-align:right; margin-right:10%; font-size:20px; }
.finalizar { text-align:center; margin-top:30px; }
</style>

</head>
<body>

<header>
<h1>🛒 Tu Carrito</h1>
<a href="shop.php" class="btn">Seguir comprando</a>
</header>

<?php if ($resultado->num_rows > 0): ?>
<table>
<tr>
<th>Imagen</th><th>Producto</th><th>Cantidad</th>
<th>Precio</th><th>Subtotal</th><th>Estado</th><th>Acción</th>
</tr>

<?php while ($fila = $resultado->fetch_assoc()):
if ($fila['finalizado'] == 0):
$subtotal = $fila['precio'] * $fila['cantidad'];
$total += $subtotal;
?>

<tr>
<td><img src="<?= $fila['imagen'] ?>"></td>
<td><?= $fila['nombre'] ?></td>
<td><?= $fila['cantidad'] ?></td>
<td>₡<?= number_format($fila['precio'],2) ?></td>
<td>₡<?= number_format($subtotal,2) ?></td>
<td><?= $fila['estado'] ?? 'En carrito' ?></td>
<td>
<a href="carrito.php?eliminar=<?= $fila['id'] ?>" class="btn">Eliminar</a>
</td>
</tr>

<?php endif; endwhile; ?>
</table>

<div class="total">
<strong>Total: ₡<?= number_format($total,2) ?></strong>
</div>

<div class="finalizar">
<form method="POST" enctype="multipart/form-data">

<?php if ($puede_fiado): ?>
<label>
    <input type="checkbox" name="fiado" id="fiado_check">
    Pagar a crédito (fiado)
</label>
<br><br>
<?php endif; ?>

<select name="tipo_pago" id="tipo_pago">
    <option value="">Seleccione método de pago</option>
    <option value="efectivo">Efectivo</option>
    <option value="sinpe">SINPE</option>
    <option value="transferencia">Transferencia</option>
</select>

<br><br>

<div id="comprobante_container" style="display:none;">
    <label>Subir comprobante:</label><br>
    <input type="file" name="comprobante" id="comprobante">
</div>

<br>

<button type="submit" name="finalizar" class="btn">
Finalizar compra
</button>

</form>
</div>

<?php else: ?>
<p style="text-align:center;">Carrito vacío</p>
<?php endif; ?>

<!-- 🔥 HISTORIAL -->
<div style="width:80%; margin:50px auto;">
<h2 style="text-align:center;">📜 Historial de Compras</h2>

<?php if ($historial->num_rows > 0): ?>
<table>
<tr>
<th>Imagen</th>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
<th>Estado</th>
<th>Fecha</th>
</tr>

<?php while ($fila = $historial->fetch_assoc()):
$subtotal = $fila['precio'] * $fila['cantidad'];
?>

<tr>
<td><img src="<?= $fila['imagen'] ?>"></td>
<td><?= $fila['nombre'] ?></td>
<td><?= $fila['cantidad'] ?></td>
<td>₡<?= number_format($fila['precio'],2) ?></td>
<td>₡<?= number_format($subtotal,2) ?></td>
<td><?= $fila['estado'] ?? 'Procesando' ?></td>
<td><?= $fila['fecha_orden'] ?? '-' ?></td>
</tr>

<?php endwhile; ?>
</table>

<?php else: ?>
<p style="text-align:center;">No tienes compras aún</p>
<?php endif; ?>
</div>

<script>
const tipoPago = document.getElementById('tipo_pago');
const comprobanteContainer = document.getElementById('comprobante_container');
const comprobanteInput = document.getElementById('comprobante');
const fiadoCheck = document.getElementById('fiado_check');

tipoPago.addEventListener('change', function() {

    if (this.value === 'sinpe' || this.value === 'transferencia') {
        comprobanteContainer.style.display = 'block';
        comprobanteInput.required = true;
    } else {
        comprobanteContainer.style.display = 'none';
        comprobanteInput.required = false;
        comprobanteInput.value = "";
    }

});

if (fiadoCheck) {
    fiadoCheck.addEventListener('change', function() {
        if (this.checked) {
            tipoPago.disabled = true;
            comprobanteContainer.style.display = 'none';
            comprobanteInput.required = false;
        } else {
            tipoPago.disabled = false;
        }
    });
}
</script>

</body>
</html>