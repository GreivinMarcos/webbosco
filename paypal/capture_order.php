<?php
// paypal/capture_order.php
header('Content-Type: application/json');
session_start();
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/../conexion.php'; // ruta a tu conexion (ajusta si hace falta)

// Recibe JSON { paypalOrderId: "...", orden_id: X }
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'No input']); exit;
}

$paypalOrderId = $input['paypalOrderId'] ?? null;
$orden_id = isset($input['orden_id']) ? intval($input['orden_id']) : 0;

if (!$paypalOrderId || !$orden_id) {
    echo json_encode(['error' => 'faltan parametros']); exit;
}

$token = paypal_get_access_token();
if (!$token) {
    echo json_encode(['error' => 'No token']); exit;
}

$capture_url = paypal_base_url() . "/v2/checkout/orders/" . urlencode($paypalOrderId) . "/capture";

$ch = curl_init($capture_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $token"
]);
$res = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode >= 200 && $httpcode < 300) {
    $data = json_decode($res, true);
    // Verificar estado
    $status = $data['status'] ?? '';
    if (strtoupper($status) === 'COMPLETED' || strtoupper($status) === 'APPROVED') {
        // Actualizar tabla ordenes: marcar pagado/finalizado/tipo_pago
        $tipo_pago = 'paypal';
        $pagado = 1;
        $finalizado = 1;
        $estado = 'completada'; // o 'pendiente' según tu lógica
        $datos_pago = json_encode($data, JSON_UNESCAPED_UNICODE);

        $sql = "UPDATE ordenes SET tipo_pago = ?, pagado = ?, finalizado = ?, estado = ?, datos_pago = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiisi", $tipo_pago, $pagado, $finalizado, $estado, $datos_pago, $orden_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'ok', 'detail' => $data]);
        } else {
            echo json_encode(['error' => 'No se pudo actualizar orden en BD', 'db_error' => $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'Pago no completado', 'detail' => $data]);
    }
} else {
    echo json_encode(['error' => 'capture failed', 'http' => $httpcode, 'response' => $res]);
}
