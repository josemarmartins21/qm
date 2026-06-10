<?php
include "../config/database.php";

$sql = "UPDATE planos 
        SET nome = ?, preco = ?, descricao = ?
        WHERE plan_id = ?";

$qmanager_stmt = $qmanager->prepare($sql);
$qmanager_stmt->bind_param(
    "sdsi",
    $_POST['nome'],
    $_POST['preco'],
    $_POST['descricao'],
    $_POST['id']
);

$qmanager_stmt->execute();

header("Location: index.php");
