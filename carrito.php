<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['id_usuario'];

// Eliminar producto individual (solo si NO está finalizado)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM carrito 
                  WHERE id = $id 
                  AND usuario = '$usuario' 
                  AND finalizado = 0");
    header("Location: carrito.php");
    exit;
}

// Finalizar compra
if (isset($_POST['finalizar'])) {

    // SOLO productos activos
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

        // Crear orden
        $sql_orden = "INSERT INTO ordenes 
            (usuario_id, id_usuario, producto, total, pagado, finalizado, estado, fecha_orden)
            VALUES (?, ?, ?, ?, 0, 0, 'pendiente', NOW())";

        $stmt = $conn->prepare($sql_orden);
        $stmt->bind_param("iisd", $id_usuario, $id_usuario, $productos, $total);
        $stmt->execute();

        $orden_id = $stmt->insert_id;

        // 🔥 EN VEZ DE BORRAR → MARCAMOS COMO FINALIZADO
        $conn->query("UPDATE carrito 
                      SET finalizado = 1 
                      WHERE usuario = '$usuario' 
                      AND finalizado = 0");

        header("Location: finalizar_orden.php?orden_id=$orden_id");
        exit;
    }
}

// Mostrar SOLO carrito activo
$resultado = $conn->query("SELECT * FROM carrito 
                            WHERE usuario = '$usuario' 
                            AND finalizado = 0");

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras - BoscoBox</title>
    <style>
        body {
            background: #0f0f0f;
            color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
        }
        header {
            background: #111;
            padding: 15px;
            text-align: center;
            border-bottom: 3px solid #00bcd4;
        }
        table {
            width: 80%;
            margin: 40px auto;
            border-collapse: collapse;
            background: #1a1a1a;
            border-radius: 10px;
        }
        th, td {
            padding: 15px;
            text-align: center;
        }
        th {
            color: #00bcd4;
        }
        img {
            width: 80px;
            border-radius: 10px;
        }
        .btn {
            background: #00bcd4;
            color: #111;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background: #0097a7;
        }
        .total {
            text-align: right;
            margin-right: 10%;
            font-size: 20px;
            margin-top: 20px;
        }
        .finalizar-compra {
            text-align: center;
            margin-top: 30px;
        }
        footer {
            background: #111;
            color: #777;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
            border-top: 2px solid #00bcd4;
        }
    </style>
</head>
<body>

<header>
    <h1>🛒 Tu Carrito FitBar</h1>
    <a href="shop.php" class="btn">Seguir comprando</a>
</header>

<?php if ($resultado->num_rows > 0): ?>
<table>
    <tr>
        <th>Imagen</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Subtotal</th>
        <th>Acción</th>
    </tr>

    <?php while ($fila = $resultado->fetch_assoc()):
        $subtotal = $fila['precio'] * $fila['cantidad'];
        $total += $subtotal;
    ?>
    <tr>
        <td><img src="<?php echo $fila['imagen']; ?>"></td>
        <td><?php echo $fila['nombre']; ?></td>
        <td><?php echo $fila['cantidad']; ?></td>
        <td>₡<?php echo number_format($fila['precio'], 2); ?></td>
        <td>₡<?php echo number_format($subtotal, 2); ?></td>
        <td>
            <a href="carrito.php?eliminar=<?php echo $fila['id']; ?>" class="btn">
                Eliminar
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<div class="total">
    <strong>Total: ₡<?php echo number_format($total, 2); ?></strong>
</div>

<div class="finalizar-compra">
    <form method="POST">
        <button type="submit" name="finalizar" class="btn" style="font-size:18px;padding:12px 25px;">
            💳 Finalizar compra
        </button>
    </form>
</div>

<?php else: ?>
<p style="text-align:center; margin-top:50px;">Tu carrito está vacío 🛍️</p>
<?php endif; ?>

<footer>
    ©️ 2025 BoscoBox - Tienda Oficial
</footer>

</body>
</html>