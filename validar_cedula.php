<?php
require_once 'conexion.php';

if (isset($_POST['cedula'])) {
    $cedula = trim($_POST['cedula']);

    $query = $conn->prepare("SELECT id FROM usuarios WHERE cedula = ?");
    $query->bind_param("s", $cedula);
    $query->execute();
    $resultado = $query->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode(['existe' => true]);
    } else {
        echo json_encode(['existe' => false]);
    }
}
?>
