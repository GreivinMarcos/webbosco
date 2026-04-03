<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['admin'] !== 'superadmin') {
    header("Location: ../login.php");
    exit;
}

// FILTROS
$estado_filtro = $_GET['estado_filtro'] ?? "";
$pago_filtro   = $_GET['pago_filtro'] ?? "";
$fiado_filtro  = $_GET['fiado_filtro'] ?? "";
$fecha_desde = $_GET['fecha_desde'] ?? "";
$fecha_hasta = $_GET['fecha_hasta'] ?? "";


// EXPORTAR EXCEL (CON FILTROS + FIADO)
if (isset($_GET['exportar']) && $_GET['exportar'] === "excel") {

    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=ordenes_boscobox.xls");

    echo "ID\tUsuario\tProductos\tTotal\tTipo Pago\tEstado\tFiado\tValidado\tFecha\n";

    $where = "WHERE 1=1";
    if ($fecha_desde && $fecha_hasta) {
        $where .= " AND DATE(o.fecha_orden) BETWEEN '$fecha_desde' AND '$fecha_hasta'";
    }
    if ($estado_filtro) $where .= " AND o.estado='$estado_filtro'";
    if ($pago_filtro)   $where .= " AND o.tipo_pago='$pago_filtro'";
    if ($fiado_filtro !== "") $where .= " AND o.fiado='$fiado_filtro'";

    $export_query = "
        SELECT o.comprobante, o.estado,o.fecha_orden, o.finalizado, o.id, o.id_usuario, o.pagado, o.producto, o.tipo_pago, o.total, o.usuario_id, o.validado, u.nombre_completo, u.puede_fiado
        FROM ordenes o
        INNER JOIN usuarios u ON o.usuario_id = u.id
        $where
        ORDER BY o.fecha_orden DESC
    ";

    $r = $conn->query($export_query);
    while ($row = $r->fetch_assoc()) {
        echo $row['id']."\t".
             $row['nombre_completo']."\t".
             $row['producto']."\t".
             "₡".number_format($row['total'],2)."\t".
             $row['tipo_pago']."\t".
             $row['estado']."\t".
             ($row['fiado']?'Sí':'No')."\t".
             ($row['validado']?'Sí':'No')."\t".
             $row['fecha_orden']."\n";
    }
    exit;
}

// CONSULTA PRINCIPAL
$query = "
SELECT o.id,u.nombre_completo usuario,o.producto,o.total,o.tipo_pago,o.estado,o.fecha_orden,o.fecha_pago,u.puede_fiado,o.validado,o.comprobante,o.fiado
FROM ordenes o
INNER JOIN usuarios u ON o.usuario_id = u.id
WHERE 1=1
";

$params=[];$types="";
if($fecha_desde && $fecha_hasta){
    $query.=" AND DATE(o.fecha_orden) BETWEEN ? AND ?";
    $params[]=$fecha_desde;
    $params[]=$fecha_hasta;
    $types.="ss";
}
if($estado_filtro){$query.=" AND o.estado=?";$params[]=$estado_filtro;$types.="s";}
if($pago_filtro){$query.=" AND o.tipo_pago=?";$params[]=$pago_filtro;$types.="s";}
if($fiado_filtro!==""){$query.=" AND o.fiado=?";$params[]=$fiado_filtro;$types.="i";}
$query.=" ORDER BY o.fecha_orden DESC";

$stmt=$conn->prepare($query);
if($params)$stmt->bind_param($types,...$params);
$stmt->execute();
$resultado=$stmt->get_result();


// ACTUALIZAR ORDEN
if (isset($_POST['actualizar_estado'])) {

    $id = intval($_POST['id_orden']);
    $estado = $_POST['estado'];
    $fiado = isset($_POST['fiado']) ? 1 : 0;
    $validado = isset($_POST['validado']) ? 1 : 0;

// 🔒 VALIDACIÓN REAL
$sql_check_val = "SELECT comprobante FROM ordenes WHERE id=?";
$stmt_val = $conn->prepare($sql_check_val);
$stmt_val->bind_param("i", $id);
$stmt_val->execute();
$res_val = $stmt_val->get_result();
$data_val = $res_val->fetch_assoc();

if ($data_val['comprobante'] && $validado == 0) {
    echo "<script>alert('Debes validar el comprobante antes de guardar');window.location='ordenes.php';</script>";
    exit;
}

    // 🔍 1. Obtener estado actual de la BD (NO confiar en POST)
    $sql_check = "SELECT estado, fiado, tipo_pago FROM ordenes WHERE id=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $res = $stmt_check->get_result();
    $orden_actual = $res->fetch_assoc();

    // 🧠 2. Determinar si debe guardar fecha_pago
    $guardar_fecha_pago = false;

    if (
        $orden_actual['fiado'] == 1 && 
        $orden_actual['estado'] !== 'completada' && 
        $estado === 'completada'
    ) {
        $guardar_fecha_pago = true;
    }

    if (
        $orden_actual['tipo_pago'] === 'efectivo' && 
        $estado === 'completada'
    ) {
        $guardar_fecha_pago = true;
    }

    // ⚙️ 3. Ejecutar UPDATE según condición
    if ($guardar_fecha_pago) {

        $sql = "UPDATE ordenes 
                SET estado=?, fiado=?, validado=?, finalizado=1, fecha_pago=NOW()
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siii", $estado, $fiado, $validado, $id);

    } else {

        $finalizado = ($estado === 'completada') ? 1 : 0;

        $sql = "UPDATE ordenes 
                SET estado=?, fiado=?, validado=?, finalizado=?
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisii", $estado, $fiado, $validado, $finalizado, $id);
    }  
    $stmt->execute();

    echo "<script>window.location='ordenes.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Órdenes - BoscoBox</title>
<style>
body { background:#0f0f0f; color:#f5f5f5; font-family:'Poppins'; margin:0;}
header { background:#111; padding:15px; text-align:center; border-bottom:3px solid #00bcd4;}
header h1 { margin:0; color:#00bcd4;}
nav a { color:#00bcd4; margin:0 15px; text-decoration:none; font-weight:bold;}
table { width:90%; margin:40px auto; border-collapse:collapse; background:#1a1a1a;
border-radius:10px; overflow:hidden; box-shadow:0 0 10px rgba(0,188,212,0.3);}
th,td { padding:12px; text-align:center;}
th { background:#111; color:#00bcd4;}
tr:nth-child(even){ background:#1f1f1f;}
.estado{ padding:6px 12px; border-radius:8px; font-weight:bold;}
.pendiente{ background:#ff9800; color:#111;}
.completada{ background:#4caf50;}
.btn{ background:#00bcd4; border:none; padding:6px 12px; font-weight:bold;border-radius:8px;cursor:pointer;}
select,input[type="date"]{background:#111;color:#fff;border:1px solid #00bcd4;border-radius:6px;padding:5px;}
</style>

</head>
<body>

<header><h1>📦 Órdenes FitBar</h1>
        <nav>
            <a href="admin_dashboard.php">Inicio</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="ordenes.php">Órdenes</a>
            <a href="../logout.php">Cerrar Sesión</a>
        </nav>
</header>

<div style="text-align:center;margin-top:20px;">
<form method="GET">
Desde <input type="date" name="fecha_desde" value="<?= $fecha_desde ?>">
Hasta <input type="date" name="fecha_hasta" value="<?= $fecha_hasta ?>">
Estado 
<select name="estado_filtro">
<option value="">Todos</option>
<option value="pendiente" <?= $estado_filtro=="pendiente"?"selected":"" ?>>Pendiente</option>
<option value="completada" <?= $estado_filtro=="completada"?"selected":"" ?>>Completada</option>
<option value="cancelado" <?= $estado_filtro=="cancelado"?"selected":"" ?>>Cancelado</option>
</select>
Pago
<select name="pago_filtro">
<option value="">Todos</option>
<option value="sinpe" <?= $pago_filtro=="sinpe"?"selected":"" ?>>Sinpe</option>
<option value="efectivo" <?= $pago_filtro=="efectivo"?"selected":"" ?>>Efectivo</option>
<option value="tarjeta" <?= $pago_filtro=="tarjeta"?"selected":"" ?>>Tarjeta</option>
</select>
Crédito
<select name="fiado_filtro">
<option value="">Todos</option>
<option value="1" <?= $fiado_filtro==="1"?"selected":"" ?>>Sí</option>
<option value="0" <?= $fiado_filtro==="0"?"selected":"" ?>>No</option>
</select>
<button class="btn">Aplicar</button>
<a href="ordenes.php" class="btn">Limpiar</a>
<a href="ordenes.php?exportar=excel
&fecha_desde=<?= $fecha_desde ?>
&fecha_hasta=<?= $fecha_hasta ?>
&estado_filtro=<?= $estado_filtro ?>
&pago_filtro=<?= $pago_filtro ?>
&fiado_filtro=<?= $fiado_filtro ?>" 
class="btn" style="background:#4caf50;color:#fff;">Excel</a>

</form>
</div>

<table>
<tr>
<th>ID</th><th>Fecha</th><th>Fecha Pago</th><th>Usuario</th><th>Productos</th><th>Total</th><th>Pago</th><th>Estado</th><th>Crédito</th><th>Validado</th><th>Comprobante</th><th>Acción</th>
</tr>

<?php while($fila=$resultado->fetch_assoc()): ?>
<tr>
<td><?= $fila['id'] ?></td>
<td><?= htmlspecialchars($fila['fecha_orden']) ?></td>
<td><?= htmlspecialchars($fila['fecha_pago']) ?></td>
<td><?= htmlspecialchars($fila['usuario']) ?></td>
<td><?= htmlspecialchars($fila['producto']) ?></td>
<td>₡<?= number_format($fila['total'],2) ?></td>
<td><?= $fila['tipo_pago'] ?></td>
<td><span class="estado <?= $fila['estado'] ?>"><?= $fila['estado'] ?></span></td>
<td><?= $fila['fiado']?"Sí":"No" ?></td>
<td><?= $fila['validado']?"Sí":"No" ?></td>
<td>
<?php 
if($fila['comprobante']){ 
    $ruta = str_replace(" ", "%20", "/Tienda e-commerce Boscobox/".$fila['comprobante']);
?>
<a class="btn" href="<?= $ruta ?>" target="_blank">Ver</a>
<?php } else echo "—"; ?>
</td>
<td>
<form method="POST">
<input type="hidden" name="id_orden" value="<?= $fila['id'] ?>">
<select name="estado">
<option value="pendiente" <?= $fila['estado']=="pendiente"?"selected":"" ?>>Pendiente</option>
<option value="completada" <?= $fila['estado']=="completada"?"selected":"" ?>>Completada</option>
<option value="cancelado" <?= $fila['estado']=="Cancelado"?"selected":"" ?>>Cancelado</option>
</select>
<!--<label><input type="checkbox" name="fiado" <?= $fila['fiado']?"checked":"" ?>>Fiado</label>-->
<?php if ($fila['puede_fiado']): ?>
    <label>
        <input type="checkbox" name="fiado" <?= $fila['fiado'] ? "checked" : "" ?>>
        Crédito
    </label>
<?php else: ?>
    <span style="color:#888;">No Autorizado</span>
<?php endif; ?>
<?php if ($fila['comprobante']): ?>
    <label>
        <input type="checkbox" name="validado" value="1"
        <?= $fila['validado'] ? "checked" : "" ?> required>
        Validado
    </label>
<?php else: ?>
    <span style="color:#888;">Sin comprobante</span>
<?php endif; ?>
<button name="actualizar_estado" class="btn">Guardar</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
