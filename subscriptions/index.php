<?php
include "../config/database.php";
include "../layout/header.php";

$sql = "SELECT c.primeiro_nome, p.nome, ch.created_at
FROM client_has_plan ch
JOIN client c ON c.client_id = ch.client_client_id
JOIN planos p ON p.plan_id = ch.planos_plan_id";



$result = $qmanager->query($sql);
?>

<h4>Assinaturas</h4>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<table class="table table-striped shadow bg-white">
<tr><th>Cliente</th><th>Plano</th><th>Desde</th></tr>
<a href="../subscriptions/create.php" class="btn btn-success">Criar Assinatura</a>

<?php while($r = $result->fetch_assoc()): ?>
<tr>
<td><?= $r['primeiro_nome'] ?></td>
<td><?= $r['nome'] ?></td>
<td><?= $r['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include "../layout/footer.php"; ?>
