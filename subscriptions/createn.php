<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/database.php";


$clientes = $qmanager->query("
    SELECT client_id, primeiro_nome, ultimo_nome, email 
    FROM client 
    ORDER BY primeiro_nome
");


$planos = $qmanager->query("
    SELECT plan_id, nome, preco 
    FROM planos 
    ORDER BY nome
");

$msg = '';


?>
<form method="POST" action="store.php">

<select name="client_id" class="form-select" required>
<option value="">Selecione o cliente</option>
<?php while($c = $clientes->fetch_assoc()): ?>
<option value="<?= $c['client_id'] ?>">
    <?= $c['primeiro_nome'] ?>
</option>
<?php endwhile; ?>
</select>

<select name="plan_id" class="form-select mt-2" required>
<option value="">Selecione o plano</option>
<?php while($p = $planos->fetch_assoc()): ?>
<option value="<?= $p['plan_id'] ?>">
    <?= $p['nome'] ?>
</option>
<?php endwhile; ?>
</select>

<button class="btn btn-success mt-3">Criar Assinatura</button>
</form>