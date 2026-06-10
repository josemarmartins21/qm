<?php
session_start();
require "../config/database.php";


if (isset($_SESSION['user']['user_id'])) {
    $session_id = session_id();
    $ssn = $qmanager->prepare("DELETE FROM user_sessions WHERE session_id = ?");
    $ssn->bind_param("s", $session_id);
    $ssn->execute();
}


$_SESSION = array();


if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destrói a sessão
session_destroy();

// Redireciona para o login
header("Location:login.php");
exit();