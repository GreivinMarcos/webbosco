<?php
session_start();
include('conexion.php');

// Verifica si el usuario está logueado
$usuario_logueado = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Si se envía una compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    if (!$usuario_logueado) {
        header("Location: login.php");
        exit;
    }

    $producto_id = intval($_POST['producto_id']);
    $cantidad = 1; // Por defecto una unidad

    // Obtener datos del producto
    $sql = "SELECT nombre, precio, imagen FROM inventario WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();

    if ($producto) {
        // Guardar en el carrito (tabla carrito)
        $sql_insert = "INSERT INTO carrito (usuario, producto_id, nombre, precio, cantidad, imagen, fecha_agregado)
                       VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param(
            "sisdis",
            $usuario_logueado,
            $producto_id,
            $producto['nombre'],
            $producto['precio'],
            $cantidad,
            $producto['imagen']
        );
        $stmt_insert->execute();

        // Mensaje visual
        echo "<script>alert('Producto agregado al carrito'); window.location='shop.php';</script>";
    }
}

// Filtro de categoría
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'Todos';

// Consulta productos
if ($categoria === 'Todos') {
    $query = "SELECT * FROM inventario ORDER BY fecha_registro DESC";
} else {
    $query = "SELECT * FROM inventario WHERE categoria = '$categoria' ORDER BY fecha_registro DESC";
}
$resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido al FitBar</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #0f0f0f;
            color: #f5f5f5;
        }

        header {
            background: linear-gradient(90deg, #111, #1a1a1a);
            padding: 20px 0;
            text-align: center;
            border-bottom: 3px solid #00bcd4;
        }

        header h1 {
            color: #00bcd4;
            margin: 0;
        }

        nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 10px;
            gap: 10px;
        }

        nav button {
            background: #1e1e1e;
            color: #f5f5f5;
            border: 1px solid #00bcd4;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        nav button:hover, nav button.active {
            background: #00bcd4;
            color: #111;
        }

        main {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .producto {
            background: #1a1a1a;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .producto:hover {
            transform: scale(1.04);
            box-shadow: 0 0 20px rgba(0,188,212,0.3);
        }

        .producto img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .producto h3 {
            color: #00bcd4;
            margin-bottom: 5px;
        }

        .producto p {
            font-size: 14px;
            color: #ccc;
            min-height: 50px;
        }

        .precio {
            font-size: 18px;
            color: #fff;
            font-weight: bold;
            margin: 10px 0;
        }

        .btn-comprar {
            background: #00bcd4;
            color: #111;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-comprar:hover {
            background: #0097a7;
            color: #fff;
        }

        footer {
            background: #111;
            color: #777;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
            border-top: 2px solid #00bcd4;
        }

        .menu-superior {
            text-align: right;
            padding-right: 30px;
        }

        .menu-superior a {
            color: #00bcd4;
            margin-left: 15px;
            text-decoration: none;
        }

        .menu-superior a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <div class="menu-superior">
        <?php if ($usuario_logueado): ?>
            Bienvenido, <?php echo $_SESSION['nombre']; ?> |
            <a href="index.php"> Inicio</a> |
            <a href="carrito.php">🛒 Carrito</a> |
            <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php">Iniciar sesión</a>
        <?php endif; ?>
    </div>
    <h1>🏋️‍♂️ Bienvenido al FitBar</h1>
    <nav>
        <button onclick="filtrar('Todos')" class="active">Todos</button>
        <button onclick="filtrar('General')">General</button>
        <button onclick="filtrar('Batidos')">Batidos</button>
        <button onclick="filtrar('Snacks')">Snacks</button>
        <button onclick="filtrar('Bowls')">Bowls</button>
        <button onclick="filtrar('Suplementos')">Suplementos</button>
        <button onclick="filtrar('Ropa')">Ropa</button>
        <button onclick="filtrar('Adicional')">Adicional</button>
    </nav>
</header>

<main id="productos">
    <?php
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            echo "<div class='producto'>
                    <img src='{$fila['imagen']}' alt='{$fila['nombre']}'>
                    <h3>{$fila['nombre']}</h3>
                    <p>{$fila['descripcion']}</p>
                    <div class='precio'>₡" . number_format($fila['precio'], 2) . "</div>
                    <form method='POST'>
                        <input type='hidden' name='producto_id' value='{$fila['id']}'>
                        <button type='submit' class='btn-comprar'>Comprar</button>
                    </form>
                  </div>";
        }
    } else {
        echo "<p style='text-align:center;grid-column:1/-1;'>No hay productos en esta categoría.</p>";
    }
    ?>
</main>

<footer>
    © 2025 BoscoBox - Tienda Oficial
</footer>

<script>
    function filtrar(categoria) {
        const botones = document.querySelectorAll('nav button');
        botones.forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');
        window.location.href = 'shop.php?categoria=' + categoria;
    }
</script>

</body>
</html>
