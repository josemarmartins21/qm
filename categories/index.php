<?php
include "../config/database.php";
include "../layout/header.php";

$result = $qmanager->query("SELECT * FROM categoria");
?>

<div class="d-flex justify-content-between mb-3">
<h4 class="fw-bold">Categorias de Planos</h4>
<a href="create.php" class="btn btn-primary">Nova Categoria</a>
</div>

<table class="table table-bordered shadow bg-white">
<tr><th>Nome</th><th>Ações</th></tr>

<?php while($c = $result->fetch_assoc()): ?>
<tr>
<td><?= $c['nome'] ?></td>
<td>
<a href="edit.php?id=<?= $c['categoria_id'] ?>" class="btn btn-warning btn-sm">Editar</a>
<a href="delete.php?id=<?= $c['categoria_id'] ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Excluir categoria?')">Excluir</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<?php include "../layout/footer.php"; ?>
