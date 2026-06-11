<?php
include "../config/database.php";
include_once __DIR__ ."/../validators/input-validators.php";


$nomeCompleto = $_POST['primeiro_nome'] . " ". $_POST['ultimo_nome'];


if (hasNumber($nomeCompleto)) {
    header('Location: index.php');
    exit;
}

$sql = "UPDATE client SET primeiro_nome=?, ultimo_nome=?, telefone=? WHERE client_id=?";

$qmanager_stmt = $qmanager->prepare($sql);

$qmanager_stmt->bind_param("sssi",
    $_POST['primeiro_nome'],
    $_POST['ultimo_nome'],
    $_POST['telefone'],
    $_POST['id']
);

$qmanager_stmt->execute();

header("Location: index.php");
