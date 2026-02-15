<?php
session_start();
include('../conexion.php');

// Verificar si el usuario es admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'si') {
    header("Location: ../login.php");
    exit();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensaje = "Usuario eliminado correctamente.";
    } else {
        $mensaje = "Error al eliminar el usuario.";
    }
    $stmt->close();
}

// Cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_pass'], $_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $nuevo_pass = password_hash($_POST['nuevo_pass'], PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_pass, $user_id);
    if ($stmt->execute()) {
        $mensaje = "Contraseña actualizada correctamente.";
    } else {
        $mensaje = "Error al actualizar contraseña.";
    }
    $stmt->close();
}

// 🔥 NUEVO: Cambiar si el usuario es admin ("si"/"no")
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_admin'], $_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $nuevo_admin = ($_POST['cambiar_admin'] === 'si') ? 'si' : 'no';

    $sql = "UPDATE usuarios SET admin = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_admin, $user_id);

    if ($stmt->execute()) {
        $mensaje = "Rol de administrador actualizado.";
    } else {
        $mensaje = "Error al actualizar rol.";
    }
    $stmt->close();
}

// Obtener todos los usuarios
$result = $conn->query("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | BoscoBox - Admin</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: url('../img/crossfit-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            margin: 0;
        }
        .overlay { background: rgba(0,0,0,0.75); position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1; }
        header { background: rgba(0,0,0,0.85); text-align:center; padding:15px; }
        header img { height: 80px; }
        nav { background: rgba(20,20,20,0.9); text-align:center; padding:10px 0; }
        nav a { color:#fff; text-decoration:none; margin:0 15px; font-weight:600; }
        nav a:hover { color:#ffc107; }
        .container { max-width:1100px; margin:40px auto; background:rgba(0,0,0,0.65); padding:30px; border-radius:10px;  overflow-x: auto; }
        h2 { text-align:center; color:#ffc107; }
        table { width:100%; border-collapse:collapse; color:#fff; }
        th, td { border:1px solid rgba(255,255,255,0.1); padding:10px; text-align:left; }
        th { background:rgba(255,193,7,0.1); }
        .btn { padding:5px 10px; border-radius:6px; text-decoration:none; font-weight:600; }
        .btn-danger { background:#dc3545; color:#fff; }
        .btn-warning { background:#ffc107; color:#111; }
        .mensaje { text-align:center; background:rgba(255,255,255,0.1); padding:8px; border-radius:6px; margin-bottom:15px; }
        footer { text-align:center; color:#ccc; padding:12px; margin-top:25px; }

    </style>
</head>
<body>
<div class="overlay"></div>
<header><img src="../img/logo.png" alt="BoscoBox"></header>

<nav>
    <a href="admin_dashboard.php">Inicio</a>
    <a href="usuarios.php">Usuarios</a>
    <a href="inventario.php">Inventario</a>
    <a href="ordenes.php">Órdenes</a>
    <a href="../logout.php">Cerrar sesión</a>
</nav>

<div class="container">
    <h2>Usuarios Registrados</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Cédula</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Admin</th>
                <th>Fecha Registro</th>
                <th>Cambio de Contraseña</th>
                <th>Admin</th>
                <th>Eliminar Usuario</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($u = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                <td><?= htmlspecialchars($u['cedula']) ?></td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td><?= htmlspecialchars($u['telefono']) ?></td>
                <td><?= htmlspecialchars($u['admin']) ?></td>
                <td><?= $u['fecha_registro'] ?></td>
                <td>

                    <!-- FORM CAMBIAR CONTRASEÑA -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <input type="password" name="nuevo_pass" placeholder="Nueva contraseña" required minlength="8">
                        <button class="btn btn-warning" type="submit">Cambiar</button>
                    </form></td>
                <td>
                    <!-- 🔥 NUEVO: FORM CAMBIAR ADMIN SI/NO -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <select name="cambiar_admin" required>
                            <option value="si" <?= $u['admin'] === 'si' ? 'selected' : '' ?>>si</option>
                            <option value="no" <?= $u['admin'] === 'no' ? 'selected' : '' ?>>no</option>
                        </select>
                        <button class="btn btn-warning" type="submit">Actualizar</button>
                    </form>
                </td>
                <td>
                    <a class="btn btn-danger" href="?eliminar=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar usuario?');">Eliminar</a>

                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>&copy; <?= date('Y') ?> BoscoBox | <a href="../contacto.php" style="color:#ffc107;">Contacto</a></footer>
</body>
</html>
