<?php
include "../config/database.php";

$sql = "INSERT INTO planos (nome, preco, descricao) VALUES (?, ?, ?)";

$qmanager_stmt = $qmanager->prepare($sql);
$qmanager_stmt->bind_param(
    "sds",
    $_POST['nome'],
    $_POST['preco'],
    $_POST['descricao']
);

$qmanager_stmt->execute();

header("Location: index.php");
