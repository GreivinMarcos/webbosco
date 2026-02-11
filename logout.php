<?php
// logout.php — Cierra sesión tanto para usuarios como administradores
session_start();

// Limpia todas las variables de sesión
$_SESSION = [];

// Destruye la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruye la sesión completamente
session_destroy();

// Redirige según el tipo de usuario
if (isset($_SESSION['admin']) && $_SESSION['admin'] === 'si') {
    header("Location: admin/login.php");
} else {
    header("Location: login.php");
}
exit;
?>
