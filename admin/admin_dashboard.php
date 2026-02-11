<?php
session_start();
include('../conexion.php');

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['usuario']) || $_SESSION['admin'] != 'si') {
    header('Location: ../login.php');
    exit;
}

// Consultas de resumen
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'] ?? 0;
$totalProductos = $conn->query("SELECT COUNT(*) AS total FROM inventario")->fetch_assoc()['total'] ?? 0;
$totalOrdenes = $conn->query("SELECT COUNT(*) AS total FROM ordenes")->fetch_assoc()['total'] ?? 0;
$ordenesPendientes = $conn->query("SELECT COUNT(*) AS total FROM ordenes WHERE estado = 'pendiente'")->fetch_assoc()['total'] ?? 0;
$ordenesCompletadas = $conn->query("SELECT COUNT(*) AS total FROM ordenes WHERE estado = 'completada'")->fetch_assoc()['total'] ?? 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - FitBar</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../img/watermark-crossfit.png') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        header {
            text-align: center;
            padding: 20px;
        }

        header img {
            width: 120px;
        }

        nav {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            display: flex;
            justify-content: center;
            gap: 30px;
            border-radius: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            color: #ff9900;
        }

        .container {
            margin-top: 40px;
        }

        .card {
            border-radius: 15px;
            background-color: rgba(0,0,0,0.6);
            color: white;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        footer {
            background-color: rgba(0,0,0,0.8);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<header>
    <img src="../img/logo.png" alt="BoscoBox Logo">
    <h1>Panel Administrativo FitBar</h1>
</header>

<nav>
    <a href="usuarios.php">👥 Usuarios</a>
    <a href="inventario.php">📦 Inventario</a>
    <a href="ordenes.php">🧾 Órdenes</a>
    <a href="../logout.php">🚪 Cerrar Sesión</a>
</nav>

<div class="container">
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card p-3">
                <h3>Usuarios Registrados</h3>
                <h2><?php echo $totalUsuarios; ?></h2>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card p-3">
                <h3>Productos en Inventario</h3>
                <h2><?php echo $totalProductos; ?></h2>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card p-3">
                <h3>Órdenes Totales</h3>
                <h2><?php echo $totalOrdenes; ?></h2>
            </div>
        </div>
    </div>

    <div class="row text-center">
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h3>Órdenes Pendientes</h3>
                <h2 style="color: #ffcc00;"><?php echo $ordenesPendientes; ?></h2>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h3>Órdenes Completadas</h3>
                <h2 style="color: #00ff66;"><?php echo $ordenesCompletadas; ?></h2>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> BoscoBox - Todos los derechos reservados.</p>
    <p>Contacto: <a href="mailto:info@boscobox.com" style="color:#ffcc00;">info@boscobox.com</a> | Tel: +506 8888-8888</p>
    <p>Ubicación: <a href="https://www.google.com/maps/search/BoscoBox+Costa+Rica" target="_blank" style="color:#ffcc00;">Ver en Google Maps</a></p>
</footer>

</body>
</html>
