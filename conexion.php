<?php
$conn = new mysqli('localhost', 'root', '', 'boscobox_fitbar');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
