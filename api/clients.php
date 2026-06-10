<?php
require "auth.php";

$res = $qmanager->query("SELECT client_id, primeiro_nome, status FROM client");
$data = [];

while($c=$res->fetch_assoc()){
    $data[] = $c;
}

echo json_encode($data);
