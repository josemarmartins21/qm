<?php
include "../config/database.php";
$qmanager->query("DELETE FROM client WHERE client_id=".$_GET['id']);
header("Location: index.php");
