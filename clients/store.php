<?php
include "../config/database.php";


$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO client 
(primeiro_nome, ultimo_nome, telefone, email, password)
VALUES (?, ?, ?, ?, ?)";

$qmanager_registo = $qmanager->prepare($sql);

$qmanager_registo->bind_param(
    "sssss",
    $_POST['primeiro_nome'],
    $_POST['ultimo_nome'],
    $_POST['telefone'],
    $_POST['email'],
    $password_hash
);

$qmanager_registo->execute();

header("Location: index.php");
