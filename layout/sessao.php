<?php
//session_start();
include "../config/database.php";

// Verifica se está logado
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Pega o ID do usuário da sessão
$id = $_SESSION['user']['user_id'];

// Busca os dados atualizados no banco
$sessao = $qmanager->prepare("SELECT primeiro_nome, ultimo_nome, email, is_adm FROM users WHERE user_id = ?");
$sessao->bind_param("i", $id);
$sessao->execute();
$result = $sessao->get_result();
$user = $result->fetch_assoc();

// Define as variáveis
$nome = $user['primeiro_nome'] . ' ' . $user['ultimo_nome'];
$email = $user['email'];
$role = ($user['is_adm'] == 1) ? 'Administrador' : 'Usuário';