<?php
require "auth.php";

$res = $qmanager->query("SELECT * FROM faturas");
$data=[];

while($f=$res->fetch_assoc()){
    $data[]=$f;
}

echo json_encode($data);
