<?php
session_start();
include "../config/database.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$clie = $qmanager->prepare("
    SELECT client_id, primeiro_nome, ultimo_nome, telefone, email, password, municipio
    FROM client
    WHERE email = ?
    LIMIT 1
");

if (!$clie) {
    die("Erro SQL: " . $qmanager->error);
}

$clie->bind_param("s", $email);
$clie->execute();
$result = $clie->get_result();

if ($client = $result->fetch_assoc()) {

    if (password_verify($password, $client['password'])) {

        $_SESSION['client'] = [
            'client_id' => $client['client_id'],
            'nome'      => $client['primeiro_nome'].' '.$client['ultimo_nome'],
            'telefone'  => $client['telefone'],
            'email'     => $client['email'],
            'municipio' => $client['municipio']
        ];

        header("Location: menu.php");
        exit;
    }
}

include 'alert.php'; 
    echo "<script>
    alerta('Login inválido!', 'error');
    setTimeout(function() {
        window.location.href = 'index.php';
    }, 1500);
</script>";
   
exit;
 