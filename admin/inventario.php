<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'si') {
    header("Location: ../login.php");
    exit();
}

include('../conexion.php');

// Agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $imagen = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio = "../uploads/";
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaDestino = $directorio . $nombreImagen;
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
            $imagen = "uploads/" . $nombreImagen;
        }
    }

    $query = $conn->prepare("INSERT INTO inventario (nombre, precio, stock, descripcion, imagen, categoria) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("sdisss", $nombre, $precio, $stock, $descripcion, $imagen, $categoria);
    if ($query->execute()) {
        echo "<script>alert('✅ Producto agregado correctamente'); window.location='inventario.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Error al agregar el producto');</script>";
    }
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $query = $conn->prepare("DELETE FROM inventario WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    header("Location: inventario.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario | FitBar</title>
    <style>
        /* === CSS ORIGINAL SIN CAMBIOS === */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #0f0f0f;
            color: #f5f5f5;
        }
        header {
            background: linear-gradient(90deg, #111, #1a1a1a);
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #00bcd4;
        }
        header h1 {
            margin: 0;
            color: #00bcd4;
        }
        nav {
            margin-top: 10px;
        }
        nav a {
            color: #ccc;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
            transition: 0.3s;
        }
        nav a:hover {
            color: #00bcd4;
        }
        main {
            width: 90%;
            margin: 40px auto;
        }
        .card {
            background: #1e1e1e;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.6);
        }
        h2 {
            color: #00bcd4;
            border-left: 5px solid #00bcd4;
            padding-left: 10px;
        }
        form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        label {
            font-weight: 500;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: none;
            background: #2a2a2a;
            color: #f5f5f5;
            outline: none;
        }
        button {
            grid-column: span 2;
            background: #00bcd4;
            border: none;
            color: #fff;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1a1a1a;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #333;
        }
        th {
            background: #00bcd4;
            color: #fff;
        }
        .btn-eliminar {
            background: #e53935;
            color: #fff;
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
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

<main>

<div class="card">
    <h2>Agregar nuevo producto</h2>
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Nombre del producto:</label>
            <input type="text" name="nombre" required>
        </div>
        <div>
            <label>Precio (₡):</label>
            <input type="number" name="precio" step="0.01" required>
        </div>
        <div>
            <label>Stock:</label>
            <input type="number" name="stock" required>
        </div>
        <div>
            <label>Categoría:</label>
            <select name="categoria" required>
                <option value="General">General</option>
                <option value="Batidos">Batidos</option>
                <option value="Snacks">Snacks</option>
                <option value="Bowls">Bowls</option>
                <option value="Suplementos">Suplementos</option>
                <option value="Ropa">Ropa</option>
                <option value="Adicional">Adicional</option>
            </select>
        </div>
        <div style="grid-column: span 2;">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3"></textarea>
        </div>
        <div>
            <label>Imagen del producto:</label>
            <input type="file" name="imagen" accept="image/*">
        </div>
        <button type="submit">Agregar producto</button>
    </form>
</div>

<div class="card">
    <h2>Inventario actual</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock ingresado</th>
            <th>Disponible</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Imagen</th>
            <th>Acción</th>
        </tr>

        <?php
        $resultado = $conn->query("
            SELECT i.*, COALESCE(SUM(c.cantidad),0) AS ventas
            FROM inventario i
            LEFT JOIN carrito c ON c.producto_id = i.id
            GROUP BY i.id
            ORDER BY i.fecha_registro DESC
        ");

        while ($fila = $resultado->fetch_assoc()) {
            $disponible = $fila['stock'] - $fila['ventas'];

            echo "<tr>
                <td>{$fila['id']}</td>
                <td>{$fila['nombre']}</td>
                <td>₡" . number_format($fila['precio'], 2) . "</td>
                <td>{$fila['stock']}</td>
                <td>{$disponible}</td>
                <td>{$fila['descripcion']}</td>
                <td>{$fila['categoria']}</td>
                <td>";

            if ($fila['imagen']) {
                echo "<img src='../{$fila['imagen']}' width='70' height='70'>";
            } else {
                echo "<span style='color:#888;'>Sin imagen</span>";
            }

            echo "</td>
                <td>
                    <a class='btn-eliminar' href='?eliminar={$fila['id']}' onclick='return confirm(\"¿Eliminar producto?\")'>Eliminar</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</div>

</main>

<footer>
    ©️ 2025 BoscoBox - Sistema Administrativo
</footer>

</body>
</html>