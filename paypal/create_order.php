<?php
// paypal/create_order.php
header('Content-Type: application/json');
session_start();
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/../conexion.php'; // ruta a tu conexion (ajusta si hace falta)

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'No input']); exit;
}

$orden_id = isset($input['orden_id']) ? intval($input['orden_id']) : 0;
$total = isset($input['total']) ? number_format(floatval($input['total']), 2, '.', '') : null;
$currency = isset($input['currency']) ? $input['currency'] : 'USD'; // cambia a tu moneda si quieres

if (!$orden_id || !$total) {
    echo json_encode(['error' => 'orden_id o total inválido']); exit;
}

$token = paypal_get_access_token();
if (!$token) {
    echo json_encode(['error' => 'No se pudo obtener token PayPal']); exit;
}

$create_url = paypal_base_url() . "/v2/checkout/orders";

$body = [
    "intent" => "CAPTURE",
    "purchase_units" => [
        [
            "reference_id" => "ORDER_" . $orden_id,
            "amount" => [
                "currency_code" => $currency,
                "value" => $total
            ],
            "description" => "Orden BoscoBox #$orden_id"
        ]
    ],
    "application_context" => [
        "brand_name" => "BoscoBox",
        "landing_page" => "NO_PREFERENCE",
        "user_action" => "PAY_NOW",
        "return_url" => "", // no usado en este flujo
        "cancel_url" => ""
    ]
];

$ch = curl_init($create_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $token"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
$res = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode >= 200 && $httpcode < 300) {
    $data = json_decode($res, true);
    // Devuelve el id de PayPal al front
    echo json_encode(['id' => $data['id'], 'links' => $data['links']]);
} else {
    echo json_encode(['error' => 'PayPal create order failed', 'http' => $httpcode, 'response' => $res]);
}
