<?php
include "../config/database.php";

$id = intval($_GET['id']);

$qmanager->query("
INSERT INTO pagamentos (fatura_id, valor_pago, data_pagamento, metodo)
SELECT fatura_id, valor, CURDATE(), 'Dinheiro'
FROM faturas WHERE fatura_id=$id
");

$qmanager->query("UPDATE faturas SET status='paga' WHERE fatura_id=$id");

header("Location: index.php");
