<?php include "../layout/header.php"; ?>

<h4 class="fw-bold mb-3">Criar Novo Plano</h4>

<form action="store.php" method="POST" class="card shadow p-4 col-md-6">
    <div class="mb-3">
        <label class="form-label">Nome do Plano</label>
        <input type="text" name="nome" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Preço</label>
        <input type="number" step="0.01" name="preco" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" rows="4" class="form-control"></textarea>
    </div>

    <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-success">
            <i class="bi bi-check-circle"></i> Salvar Plano
        </button>
    </div>
</form>

<?php include "../layout/footer.php"; ?>
