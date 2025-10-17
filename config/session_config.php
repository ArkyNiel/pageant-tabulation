<?php
$isLocal = isset($_SERVER['HTTP_ORIGIN']) && str_contains($_SERVER['HTTP_ORIGIN'], 'localhost');

$allowed_origins = $isLocal
    ? ["http://localhost:5173", "http://localhost:4173"]
    : ["https://ic2-tabulation-frontend.vercel.app"]; 

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 7,
    'path' => '/',
    'domain' => $isLocal ? '' : 'lightslategrey-swallow-561635.hostingersite.com',
    'secure' => !$isLocal,
    'httponly' => true,
    'samesite' => $isLocal ? 'Lax' : 'None', 
]);

session_start();
