<?php
session_start();

/*
  Permitir acesso se:
  - cliente estiver logado
  - OU admin estiver logado
*/
if (!isset($_SESSION['client']) && !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include "../config/database.php";
include "../layout/header_client.php";

/* =====================
   QUEM ESTÁ ACESSANDO
===================== */
if (isset($_SESSION['client'])) {
    $nome = $_SESSION['client']['primeiro_nome'];
    $tipo = "Cliente";
} else {
    $nome = $_SESSION['user']['primeiro_nome'];
    $tipo = "Administrador";
}
?>

<div class="container mt-4">
<h4>Portal do Cliente</h4>
<p class="text-muted">
Acessado por: <strong><?= htmlspecialchars($nome) ?></strong> (<?= $tipo ?>)
</p>

<div class="row g-3">

<div class="col-md-4">
<div class="card shadow p-3">
<h6>Faturas</h6>
<a href="faturas.php" class="btn btn-warning btn-sm">Ver faturas</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3">
<h6>Pagamentos</h6>
<a href="pagamentos.php" class="btn btn-success btn-sm">Ver pagamentos</a>
</div>
</div>

<div class="col-md-4">
<div class="card shadow p-3">
<h6>Status do Serviço</h6>
<span class="badge bg-success">Ativo</span>
</div>
</div>

</div>
</div>

<?php include "../layout/footer.php"; ?>
