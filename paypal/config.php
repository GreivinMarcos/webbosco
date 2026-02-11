<?php
// paypal/config.php
// RUTA: paypal/config.php

// Usa sandbox para pruebas: set 'sandbox' o 'live'
define('PAYPAL_ENV', 'sandbox');

// Pega aquí tus credenciales de PayPal (obtenidas en developer.paypal.com)
// IMPORTANT: No subas estas credenciales a repositorios públicos.
define('PAYPAL_CLIENT_ID', 'TU_PAYPAL_CLIENT_ID_AQUI');
define('PAYPAL_SECRET', 'TU_PAYPAL_SECRET_AQUI');

function paypal_base_url(){
    return (PAYPAL_ENV === 'live') ? "https://api-m.paypal.com" : "https://api-m.sandbox.paypal.com";
}

// Obtiene Access Token (v2/oauth2/token)
function paypal_get_access_token(){
    $url = paypal_base_url() . "/v1/oauth2/token";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Accept-Language: en_US"]);
    $res = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    $data = json_decode($res, true);
    return $data['access_token'] ?? false;
}
