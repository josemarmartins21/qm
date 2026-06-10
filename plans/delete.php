<?php
include "../config/database.php";

$id = intval($_GET['id']);

$qmanager_stmt = $qmanager->prepare(
    "DELETE FROM planos WHERE plan_id = ?"
);
$qmanager_stmt->bind_param("i", $id);
$qmanager_stmt->execute();

header("Location: index.php");
