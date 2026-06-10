<?php
include "../config/database.php";
include "../layout/header.php";

$result = $qmanager->query("SELECT * FROM users");
?>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
<div class="d-flex justify-content-between mb-3">
<h4 class="fw-bold">Usuários do Sistema</h4>
<a href="create.php" class="btn btn-primary">Novo Usuário</a>
</div>

<table class="table table-hover shadow bg-white">
<tr>
<th>Nome</th><th>Email</th><th>Admin</th><th>Ações</th>
</tr>

<?php while($u = $result->fetch_assoc()): ?>
<tr>
<td><?= $u['primeiro_nome']." ".$u['ultimo_nome'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= $u['is_adm'] ? 'Sim' : 'Não' ?></td>
<td>
<a href="edit.php?id=<?= $u['user_id'] ?>" class="btn btn-warning btn-sm">Editar</a>
<a href="delete.php?id=<?= $u['user_id'] ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Excluir usuário?')">Excluir</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<?php include "../layout/footer.php"; ?>
