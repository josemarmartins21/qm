<?php
require "../config/database.php";

$qmanager->query("
UPDATE faturas 
SET status='vencida'
WHERE status='pendente' AND data_vencimento < CURDATE()
");


$qmanager->query("
UPDATE client 
SET status='suspenso'
WHERE client_id IN (
    SELECT client_id FROM faturas WHERE status='vencida'
)
");
