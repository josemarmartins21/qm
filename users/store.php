<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
include "../config/database.php";

$senha = password_hash($_POST['password'], PASSWORD_DEFAULT);
$is_adm = isset($_POST['is_adm']) ? 1 : 0;

$sql = "INSERT INTO users (primeiro_nome, ultimo_nome, email, password, is_adm)
VALUES (?, ?, ?, ?, ?)";

$qmanager_stmt = $qmanager->prepare($sql);
$qmanager_stmt->bind_param("ssssi",
$_POST['primeiro_nome'],
$_POST['ultimo_nome'],
$_POST['email'],
$senha,
$is_adm);

$qmanager_stmt->execute();
header("Location: index.php");
