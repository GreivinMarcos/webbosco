<?php
session_start();
include('conexion.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Verificar si hay una orden activa
if (!isset($_GET['orden_id'])) {
    header("Location: carrito.php");
    exit;
}

$orden_id = intval($_GET['orden_id']);
$mensaje = "";
$ruta_comprobante = null;

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tipo_pago = $_POST['metodo_pago'];

    // ==========================
    // SUBIDA DE COMPROBANTE
    // ==========================
    if (!empty($_FILES['comprobante']['name'])) {

        $directorio = "comprobantes/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreArchivo = time() . "_" . basename($_FILES["comprobante"]["name"]);
        $ruta_comprobante = $directorio . $nombreArchivo;

        move_uploaded_file($_FILES["comprobante"]["tmp_name"], $ruta_comprobante);
    }

    // Actualizar orden
    $sql = "UPDATE ordenes 
            SET tipo_pago = ?, 
                comprobante = ?, 
                finalizado = 1, 
                pagado = 1, 
                estado = 'pendiente' 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $tipo_pago, $ruta_comprobante, $orden_id);

    if ($stmt->execute()) {
        $mensaje = "✅ Su compra ha sido finalizada correctamente usando el método: " . ucfirst($tipo_pago);
    } else {
        $mensaje = "❌ Error al procesar la compra.";
    }
}

// Obtener orden
$query = "SELECT * FROM ordenes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $orden_id);
$stmt->execute();
$resultado = $stmt->get_result();
$orden = $resultado->fetch_assoc();

if (!$orden) {
    header("Location: carrito.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Finalizar Compra - BoscoBox</title>

<style>
body { background:#0f0f0f; color:#f5f5f5; font-family:Poppins,sans-serif; margin:0; }
header { background:linear-gradient(90deg,#111,#1a1a1a); padding:20px; text-align:center; border-bottom:3px solid #00bcd4; }
header h1 { color:#00bcd4; margin:0; }
main { width:90%; max-width:600px; margin:40px auto; background:#1a1a1a; border-radius:15px; padding:30px; text-align:center; }
.detalle { background:#111; border-radius:10px; padding:15px; margin-bottom:20px; }
select,input,button { width:80%; padding:10px; margin:10px auto; display:block; border-radius:8px; }
button { background:#00bcd4; font-weight:bold; cursor:pointer; }
.mensaje { margin-top:20px; }
</style>

<script>
function mostrarMensaje() {
    let metodo = document.getElementById("metodo_pago").value;

    document.getElementById("sinpe").style.display = "none";
    document.getElementById("transferencia").style.display = "none";
    document.getElementById("efectivo").style.display = "none";
    document.getElementById("archivo").style.display = "none";

    if (metodo === "sinpe movil") {
        document.getElementById("sinpe").style.display = "block";
        document.getElementById("archivo").style.display = "block";
    }
    if (metodo === "trasferenia bancaria") {
        document.getElementById("transferencia").style.display = "block";
        document.getElementById("archivo").style.display = "block";
    }
    if (metodo === "efectivo") {
        document.getElementById("efectivo").style.display = "block";
    }
}
</script>

</head>
<body>

<header>
<h1>💳 Finalizar Compra</h1>
</header>

<main>

<h2>Resumen</h2>

<div class="detalle">
<p><strong>Producto:</strong> <?= htmlspecialchars($orden['producto']) ?></p>
<p><strong>Total:</strong> ₡<?= number_format($orden['total'],2) ?></p>
</div>

<?php if (!$orden['finalizado']): ?>
<form method="POST" enctype="multipart/form-data">

<select name="metodo_pago" id="metodo_pago" required onchange="mostrarMensaje()">
<option value="">Seleccione un método</option>
<option value="sinpe movil">SINPE Móvil</option>
<option value="efectivo">Efectivo</option>
<option value="trasferenia bancaria">Trasferencia Bancaria</option>
</select>

<div id="sinpe" style="display:none">
Enviar pago al número <strong>72197987</strong> y adjuntar comprobante.
</div>

<div id="transferencia" style="display:none">
Transferir al BAC Credomatic <strong>xxx-xxx-xxx-xxx</strong> y adjuntar comprobante.
</div>

<div id="efectivo" style="display:none">
Debe dirigirse a la caja para realizar el pago en efectivo.
</div>

<div id="archivo" style="display:none">
<input type="file" name="comprobante" accept="image/*,application/pdf">
</div>

<button type="submit">Finalizar compra</button>
</form>
<?php endif; ?>

<?php if (!empty($mensaje)): ?>
<div class="mensaje"><?= $mensaje ?></div>
<a href="carrito.php" style="color:#00bcd4">Volver</a>
<?php endif; ?>

</main>

</body>
</html>
