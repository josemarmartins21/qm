<?php
include "../config/database.php";

$qmanager_stmt = $qmanager->prepare(
"INSERT INTO categoria (nome) VALUES (?)"
);
$qmanager_stmt->bind_param("s", $_POST['nome']);
$qmanager_stmt->execute();

header("Location: index.php");
