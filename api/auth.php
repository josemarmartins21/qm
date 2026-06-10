<?php
header("Content-Type: application/json");
require "../config/database.php";

$token = $_GET['token'] ?? '';

if ($token !== "QMANAGER_API_KEY") {
    http_response_code(401);
    echo json_encode(["error"=>"Não autorizado"]);
    exit;
}
