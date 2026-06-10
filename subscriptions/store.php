<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
include "../config/database.php";

$client_id = (int) ($_POST['client_id'] ?? 0);
$plan_id   = (int) ($_POST['plan_id'] ?? 0);


if ($client_id <= 0 || $plan_id <= 0) {
    die("Cliente ou plano inválido (ID vazio)");
}


$stmt = $qmanager->prepare("SELECT client_id FROM client WHERE client_id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    die("Cliente não existe");
}


$stmt = $qmanager->prepare("SELECT plan_id FROM planos WHERE plan_id = ?");
$stmt->bind_param("i", $plan_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    die("Plano não existe");
}

/* =====================
   CRIAR ASSINATURA
===================== */
$data_inicio = date("Y-m-d");
$data_fim    = date("Y-m-d", strtotime("+30 days"));

$stmt = $qmanager->prepare("
    INSERT INTO client_has_plan
    (client_client_id, planos_plan_id, data_inicio, data_fim)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("iiss",
    $client_id,
    $plan_id,
    $data_inicio,
    $data_fim
);

$stmt->execute();

/* =====================
   CRIAR FATURA
===================== */
$stmt = $qmanager->prepare("
    INSERT INTO faturas
    (client_client_id, valor, status, data_vencimento)
    SELECT ?, preco, 'pendente', DATE_ADD(CURDATE(), INTERVAL 5 DAY)
    FROM planos WHERE plan_id = ?
");

$stmt->bind_param("ii", $client_id, $plan_id);
$stmt->execute();

header("Location: index.php?ok=1");
exit;