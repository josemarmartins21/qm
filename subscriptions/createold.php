<?php include "../layout/header.php"; ?>
<h4>Novo Cliente</h4>

<form action="store.php" method="POST" class="card p-4 shadow col-md-6">
<input name="primeiro_nome" class="form-control mb-2" placeholder="Primeiro Nome" required>
<input name="ultimo_nome" class="form-control mb-2" placeholder="Último Nome" required>
<input name="telefone" class="form-control mb-2" placeholder="Telefone">
<input name="email" type="email" class="form-control mb-3" placeholder="Email">
<button class="btn btn-success">Salvar</button>
</form>

<?php include "../layout/footer.php"; ?>
