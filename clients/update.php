<?php
include_once __DIR__ . "/../helpers/helpers.php";
include "../config/database.php";
include_once __DIR__ ."/../validators/input-validators.php";


$nomeCompleto = $_POST['primeiro_nome'] . " ". $_POST['ultimo_nome'];


if (hasNumber($nomeCompleto)) {
    header('Location: index.php');
    exit;
}

$sql = "UPDATE client SET primeiro_nome=?, ultimo_nome=?, telefone=? WHERE client_id=?";

$qmanager_stmt = $qmanager->prepare($sql);
$primeiroNome = ucfirst($_POST['primeiro_nome']);
$ultimoNome = ucfirst($_POST['ultimo_nome']);
$email = strtolower($_POST['email']);
$telefone = addString($_POST['telefone'], '+244'); 


$qmanager_stmt->bind_param("sssi",
    $primeiroNome,
    $ultimoNome,
    $telefone,
    $_POST['id']
);

$qmanager_stmt->execute();

header("Location: index.php");
