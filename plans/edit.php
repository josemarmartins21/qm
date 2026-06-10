<?php
include "../config/database.php";
include "../layout/header.php";

$id = intval($_GET['id']);
$plano = $qmanager->query(
    "SELECT * FROM planos WHERE plan_id = $id"
)->fetch_assoc();
?>

<h4 class="fw-bold mb-3">Editar Plano</h4>

<form action="update.php" method="POST" class="card shadow p-4 col-md-6">
    <input type="hidden" name="id" value="<?= $plano['plan_id'] ?>">

    <div class="mb-3">
        <label class="form-label">Nome do Plano</label>
        <input type="text" name="nome" value="<?= $plano['nome'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Preço</label>
        <input type="number" step="0.01" name="preco" value="<?= $plano['preco'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" rows="4" class="form-control"><?= $plano['descricao'] ?></textarea>
    </div>

    <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-warning">
            <i class="bi bi-save"></i> Atualizar Plano
        </button>
    </div>
</form>

<?php include "../layout/footer.php"; ?>
