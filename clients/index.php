<?php
include "../config/database.php";
include "../layout/header.php";

$result = $qmanager->query("SELECT * FROM client");
?>

<div class="d-flex justify-content-between mb-3">
  <h4>Clientes</h4>
  <a href="create.php" class="btn btn-primary">
    <i class="bi bi-plus"></i> Novo Cliente
  </a>
</div>

<table class="table table-hover shadow bg-white">
<tr>
<th>Nome</th><th>Telefone</th><th>Ações</th>
</tr>
<?php while($c = $result->fetch_assoc()): ?>
<tr>
<td><?= $c['primeiro_nome']." ".$c['ultimo_nome'] ?></td>
<td><?= $c['telefone'] ?></td>
<td>
<a href="edit.php?id=<?= $c['client_id'] ?>" class="btn btn-sm btn-warning"> <i class="bi bi-pencil"></i>Editar</a>
<a href="delete.php?id=<?= $c['client_id'] ?>" class="btn btn-sm btn-danger"
onclick="return confirm('Excluir cliente?')"> <i class="bi bi-trash"></i>Excluir</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<?php include "../layout/footer.php"; ?>
