<?php include "../layout/header.php"; ?>

<h4>Novo Usuário</h4>

<form action="store.php" method="POST" class="card p-4 shadow col-md-6">
<input name="primeiro_nome" class="form-control mb-2" placeholder="Primeiro nome" required>
<input name="ultimo_nome" class="form-control mb-2" placeholder="Último nome" required>
<input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
<input name="password" type="password" class="form-control mb-2" placeholder="Senha" required>

<div class="form-check mb-3">
<input type="checkbox" name="is_adm" class="form-check-input">
<label class="form-check-label">Administrador</label>
</div>

<button class="btn btn-success">Salvar</button>
</form>

<?php include "../layout/footer.php"; ?>
