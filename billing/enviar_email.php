<?php
require "../config/database.php";

$id = intval($_GET['id']);

$f = $qmanager->query("
SELECT f.*, c.email
FROM faturas f
JOIN client c ON c.client_id=f.client_id
WHERE f.fatura_id=$id
")->fetch_assoc();

$to = $f['email'];
$subject = "Sua fatura - QManager ISP";
$message = "Olá,\nSua fatura no valor de {$f['valor']} Kz vence em {$f['data_vencimento']}.";
$headers = "From: faturacao@qmanager.co";

mail($to, $subject, $message, $headers);

echo "Email enviado!";
