<?php

include_once __DIR__ . "/../helpers/helpers.php";
include __DIR__ ."/../config/database.php";
include_once __DIR__ ."/../validators/input-validators.php";

$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

$nomeCompleto =  $_POST['primeiro_nome'] . " ". $_POST['ultimo_nome'];
$email = strtolower(filter_input(INPUT_POST, $_POST['email'], FILTER_VALIDATE_EMAIL));


if (hasNumber($nomeCompleto)) {
    header('Location: index.php');
    exit;
}

if (hasNumber($email)) {
    header('Location: index.php');
    exit;
}

$sql = "INSERT INTO client 
(primeiro_nome, ultimo_nome, telefone, email, password)
VALUES (?, ?, ?, ?, ?)";

$qmanager_registo = $qmanager->prepare($sql);


$primeiroNome = ucfirst($_POST['primeiro_nome']);
$ultimoNome = ucfirst($_POST['ultimo_nome']);
$email = strtolower($email);
$telefone = addString($_POST['telefone'], '+244'); 

$qmanager_registo->bind_param(
    "sssss",
    $primeiroNome,
    $ultimoNome,
    $telefone,
    $email,
    $password_hash
);

$qmanager_registo->execute();

header("Location: index.php");