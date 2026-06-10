<?php
session_start();
include "../config/database.php";

$sql = "SELECT * FROM client WHERE email=?";
$qmanager_pskua = $qmanager->prepare($sql);
$qmanager_pskua->bind_param("s", $_POST['email']);
$qmanager_pskua->execute();
$c = $qmanager_pskua->get_result()->fetch_assoc();

if($c && password_verify($_POST['password'],$c['password'])){
    $_SESSION['client'] = $c;
    header("Location: dashboard.php");
}
