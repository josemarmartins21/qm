<?php
include "../config/database.php";
include "../layout/header.php";

$result = $qmanager->query("SELECT * FROM planos");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Planos de Internet</h4>
    <a href="create.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Plano
    </a>
</div>

<div class="row">
<?php while($p = $result->fetch_assoc()): ?>
    <div class="col-md-4 mb-3">
        <div class="card shadow h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold"><?= $p['nome'] ?></h5>
                <h6 class="text-success mb-2">
                    <?= number_format($p['preco'], 2, ',', '.') ?> Kz
                </h6>
                <p class="card-text text-muted">
                    <?= nl2br($p['descricao']) ?>
                </p>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="edit.php?id=<?= $p['plan_id'] ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="delete.php?id=<?= $p['plan_id'] ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Tem certeza que deseja excluir este plano?')">
                    <i class="bi bi-trash"></i> Excluir
                </a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

<?php include "../layout/footer.php"; ?>
